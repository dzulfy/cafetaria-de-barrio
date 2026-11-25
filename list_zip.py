import zipfile, json, sys
zip_path = r'C:/Users/Bahrul alakin/.gemini/antigravity/brain/5f3e67aa-3745-40f0-a427-25ae5a26debc/.user_uploaded/media_1795421085343_9bac90ed.zip'
with zipfile.ZipFile(zip_path, 'r') as z:
    entries = z.namelist()
print(json.dumps(entries, indent=2))
