Bu klasör, PHP container'ında Oracle'a bağlanabilmek için Oracle Instant Client dosyalarını **senin indirip** buraya koyman için var.

Gerekli dosyalar (Linux x86_64):
- `instantclient-basiclite-linux.x64-*.zip`
- `instantclient-sdk-linux.x64-*.zip`

İndirdikten sonra bu 2 zip'i bu klasöre bırak:
- docker/php/instantclient/

Sonra container'ı yeniden build et:
- `cd docker`
- `docker compose up -d --build`

Notlar:
- Oracle Instant Client indirme/kurulum şartları Oracle lisansına tabidir; bu repo otomatik indirme yapmaz.
- Instant Client kurulduktan sonra PHP içinde `pdo_oci`/`oci8` extension'ları aktif olur ve `DB_DRIVER=oracle` ile uygulama Oracle'a bağlanır.
