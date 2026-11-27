<?php

namespace App\Services;

use App\Models\CafeTable;
use App\Models\GuestSession;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestSessionService
{
    public const SESSION_KEY = 'guest_session_id';

    /**
     * Resolve an existing server-side guest session or create a new UUID-backed one.
     */
    public function ensure(Request $request, ?CafeTable $table = null): GuestSession
    {
        $sessionId = $request->session()->get(self::SESSION_KEY);

        if (is_string($sessionId) && Str::isUuid($sessionId)) {
            $guestSession = GuestSession::find($sessionId);

            if ($guestSession !== null) {
                if ($table !== null && $guestSession->table_id !== $table->id) {
                    $guestSession->table_id = $table->id;
                }

                if ($guestSession->last_activity_at === null || $guestSession->last_activity_at->lt(now()->subMinutes(5))) {
                    $guestSession->last_activity_at = now();
                }

                if ($guestSession->isDirty()) {
                    $guestSession->save();
                }

                return $guestSession;
            }
        }

        $guestSession = GuestSession::create([
            'id' => (string) Str::uuid(),
            'table_id' => $table?->id,
            'last_activity_at' => now(),
        ]);

        $request->session()->put(self::SESSION_KEY, $guestSession->id);

        return $guestSession;
    }

    /**
     * Return the current validated UUID from the server-side session without reading request input.
     */
    public function currentId(Request $request): ?string
    {
        $sessionId = $request->session()->get(self::SESSION_KEY);

        if (! is_string($sessionId) || ! Str::isUuid($sessionId)) {
            return null;
        }

        return $sessionId;
    }

    public function current(Request $request): ?GuestSession
    {
        $sessionId = $this->currentId($request);

        return $sessionId !== null ? GuestSession::find($sessionId) : null;
    }

    public function syncTable(Request $request, ?CafeTable $table): GuestSession
    {
        return $this->ensure($request, $table);
    }

    public function isStaff(Request $request): bool
    {
        $user = $request->user();

        return $user !== null && $user->is_active && $user->hasRole('admin', 'kasir');
    }

    public function registeredCustomer(Request $request): ?User
    {
        $user = $request->user();

        if ($user !== null && $user->is_active && ! $this->isStaff($request)) {
            return $user;
        }

        return null;
    }

    /**
     * Determine whether an order belongs to a registered customer (user_id), a guest device (guest_session_id), or cashier POS.
     *
     * @return array{user_id: int|null, guest_session_id: string|null}
     */
    public function resolveOrderOwner(Request $request, ?CafeTable $table = null, bool $isCashierOrder = false): array
    {
        if ($isCashierOrder && $this->isStaff($request)) {
            return [
                'user_id' => null,
                'guest_session_id' => null,
            ];
        }

        $customer = $this->registeredCustomer($request);
        if ($customer !== null) {
            return [
                'user_id' => $customer->id,
                'guest_session_id' => null,
            ];
        }

        $guestSession = $this->ensure($request, $table);

        return [
            'user_id' => null,
            'guest_session_id' => $guestSession->id,
        ];
    }

    /**
     * Scope order history strictly to the authenticated customer's user_id OR the guest's session UUID.
     */
    public function scopeOrdersForRequester(Request $request, Builder $query): Builder
    {
        $customer = $this->registeredCustomer($request);
        if ($customer !== null) {
            return $query->where('user_id', $customer->id);
        }

        $guestSession = $this->ensure($request);

        return $query->whereNull('user_id')->where('guest_session_id', $guestSession->id);
    }

    public function canAccessOrder(Request $request, Order $order): bool
    {
        if ($this->isStaff($request)) {
            return true;
        }

        $customer = $this->registeredCustomer($request);
        if ($customer !== null) {
            return $order->user_id !== null && (int) $order->user_id === (int) $customer->id;
        }

        $guestSessionId = $this->currentId($request);

        if ($guestSessionId === null || $order->user_id !== null || empty($order->guest_session_id)) {
            return false;
        }

        return hash_equals((string) $order->guest_session_id, $guestSessionId);
    }

    public function authorizeOrder(Request $request, Order $order): void
    {
        if (! $this->canAccessOrder($request, $order)) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }
    }
}
