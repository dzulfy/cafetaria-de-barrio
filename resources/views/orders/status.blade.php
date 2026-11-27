@extends('layouts.app')

@section('title', 'Status Pesanan — De Barrio')

@section('content')
    @include('orders.partials.status_card', ['order' => $order, 'inMockup' => false])
@endsection
