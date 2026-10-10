<?php

namespace Database\Seeders;

use App\Models\KnowledgeArticle;
use Illuminate\Database\Seeder;

/**
 * Starter support articles for the Telegram bot (uz/ru/en).
 *
 * Articles describe steps only — the actual configuration barcodes differ per model and are
 * uploaded by staff in Admin → Knowledge Base (images / PDF), so none are invented here.
 * Existing slugs are never overwritten, so admin edits survive re-seeding.
 */
class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $created = 0;

        foreach ($this->articles() as $i => [$slug, $title, $keywords, $answer]) {
            $article = KnowledgeArticle::firstOrCreate(['slug' => $slug], [
                'title'      => $title,
                'keywords'   => $keywords,
                'answer'     => $answer,
                'is_active'  => true,
                'sort_order' => $i,
            ]);
            $created += (int) $article->wasRecentlyCreated;
        }

        $this->command?->info("Knowledge base: {$created} article(s) created, " . (count($this->articles()) - $created) . ' already existed.');
    }

    private function articles(): array
    {
        return [
            [
                'add-enter-suffix',
                ['uz' => "Skanerlangandan keyin Enter (yangi qator) qo'shish", 'ru' => 'Как добавить Enter после сканирования', 'en' => 'How to add Enter after each scan'],
                ['uz' => "enter, suffiks, yangi qator, cr, keyingi qatorga o'tmaydi", 'ru' => 'enter, суффикс, перевод строки, cr, lf, не переходит на следующую строку', 'en' => 'enter, suffix, carriage return, cr, lf, new line, tab'],
                [
                    'uz' => "<p>Ko'pchilik kassa va hisob dasturlari kod oxirida Enter bo'lishini kutadi.</p><ol><li>Skaneringiz modelining qo'llanmasini (PDF) oching yoki menejerdan so'rang.</li><li>«Enter setup» / «Enable setup» sozlash shtrix-kodini skanerlang.</li><li>«Add CR suffix» (Enter qo'shish) kodini skanerlang.</li><li>«Exit setup» / «Save» kodini skanerlang.</li><li>Bloknotda tekshiring: har bir koddan keyin kursor yangi qatorga o'tishi kerak.</li></ol><p>Tab kerak bo'lsa, «Add TAB suffix» kodidan foydalaning.</p>",
                    'ru' => '<p>Большинство кассовых и учётных программ ждут Enter в конце кода.</p><ol><li>Откройте руководство (PDF) для вашей модели или запросите его у менеджера.</li><li>Отсканируйте настроечный код «Enter setup» / «Вход в настройки».</li><li>Отсканируйте код «Add CR suffix» (добавить Enter).</li><li>Отсканируйте «Exit setup» / «Save».</li><li>Проверьте в Блокноте: после каждого кода курсор должен переходить на новую строку.</li></ol><p>Если нужен Tab — используйте код «Add TAB suffix».</p>',
                    'en' => '<p>Most POS and accounting programs expect Enter at the end of the code.</p><ol><li>Open the manual (PDF) for your model or ask a manager for it.</li><li>Scan the “Enter setup” configuration barcode.</li><li>Scan “Add CR suffix” (add Enter).</li><li>Scan “Exit setup” / “Save”.</li><li>Test in Notepad: after each scan the cursor should move to a new line.</li></ol><p>If you need Tab instead, use “Add TAB suffix”.</p>',
                ],
            ],
            [
                'wrong-characters-keyboard-layout',
                ['uz' => "Skaner noto'g'ri belgilar yozadi (kirill harflar, belgilar almashgan)", 'ru' => 'Сканер вводит неправильные символы (русские буквы, перепутанные знаки)', 'en' => 'Scanner types wrong characters (Cyrillic letters, swapped symbols)'],
                ['uz' => "noto'g'ri belgilar, kirill, ruscha harflar, klaviatura tili, raskladka, kod buzilgan", 'ru' => 'неправильные символы, русские буквы, кракозябры, раскладка, язык клавиатуры, код искажён', 'en' => 'wrong characters, cyrillic, russian letters, keyboard layout, language, garbled code'],
                [
                    'uz' => "<p>USB skaner klaviatura kabi ishlaydi, shuning uchun kompyuterda rus klaviaturasi yoqilgan bo'lsa, harflar kirillda chiqadi.</p><ol><li>Skanerlashdan oldin klaviaturani ingliz tiliga (EN) o'tkazing — eng tez yechim.</li><li>Yoki dasturda kiritish tilini doimiy EN qilib qo'ying.</li><li>Doimiy yechim: qo'llanmadagi «Keyboard emulation: Alt+Numpad» (yoki «Universal keyboard») sozlash kodini skanerlang — shunda til ahamiyatsiz bo'ladi.</li><li>Raqamlar o'rniga belgilar chiqsa (masalan, ! @ #), Caps Lock/NumLock'ni tekshiring.</li></ol>",
                    'ru' => '<p>USB-сканер работает как клавиатура, поэтому при включённой русской раскладке буквы приходят кириллицей.</p><ol><li>Переключите раскладку на английскую (EN) перед сканированием — самый быстрый способ.</li><li>Или закрепите язык ввода EN в нужной программе.</li><li>Постоянное решение: отсканируйте из руководства код «Keyboard emulation: Alt+Numpad» (или «Universal keyboard») — тогда раскладка не важна.</li><li>Если вместо цифр приходят символы (! @ #) — проверьте Caps Lock / NumLock.</li></ol>',
                    'en' => '<p>A USB scanner acts as a keyboard, so with a Russian keyboard layout active the letters arrive as Cyrillic.</p><ol><li>Switch the keyboard layout to English (EN) before scanning — the quickest fix.</li><li>Or lock the input language to EN in the program you use.</li><li>Permanent fix: scan the “Keyboard emulation: Alt+Numpad” (or “Universal keyboard”) code from the manual — then the layout does not matter.</li><li>If symbols (! @ #) appear instead of digits, check Caps Lock / NumLock.</li></ol>',
                ],
            ],
            [
                'connect-bluetooth',
                ['uz' => "Skanerni Bluetooth orqali ulash (telefon, planshet, kompyuter)", 'ru' => 'Подключение сканера по Bluetooth (телефон, планшет, ПК)', 'en' => 'Connecting the scanner via Bluetooth (phone, tablet, PC)'],
                ['uz' => "bluetooth, bt, ulash, juftlash, telefon, planshet, android, iphone", 'ru' => 'bluetooth, блютуз, bt, подключить, сопряжение, телефон, планшет, android, iphone', 'en' => 'bluetooth, bt, pair, pairing, connect, phone, tablet, android, iphone'],
                [
                    'uz' => "<ol><li>Skanerni zaryadlang va yoqing.</li><li>Qo'llanmadagi «Bluetooth HID» rejimi kodini skanerlang (klaviatura rejimi).</li><li>«Pairing» (juftlash) kodini skanerlang — indikator tez miltillaydi.</li><li>Telefon/kompyuterda Bluetooth sozlamalarini oching va ro'yxatdan skanerni tanlang (nomi odatda model nomi bilan).</li><li>Ulangandan keyin bloknot yoki dasturda sinab ko'ring.</li></ol><p>Ulanmasa: skanerni boshqa qurilmadan ajrating, telefon/PC'da «qurilmani unutish»ni bosing va qaytadan juftlang.</p>",
                    'ru' => '<ol><li>Зарядите и включите сканер.</li><li>Отсканируйте из руководства код режима «Bluetooth HID» (режим клавиатуры).</li><li>Отсканируйте код «Pairing» (сопряжение) — индикатор начнёт быстро мигать.</li><li>Откройте настройки Bluetooth на телефоне/ПК и выберите сканер в списке (обычно называется по модели).</li><li>После подключения проверьте в Блокноте или вашей программе.</li></ol><p>Не подключается: отключите сканер от другого устройства, нажмите «Забыть устройство» на телефоне/ПК и выполните сопряжение заново.</p>',
                    'en' => '<ol><li>Charge and switch on the scanner.</li><li>Scan the “Bluetooth HID” mode code from the manual (keyboard mode).</li><li>Scan the “Pairing” code — the indicator starts blinking fast.</li><li>Open Bluetooth settings on the phone/PC and select the scanner (usually named after the model).</li><li>Once connected, test in Notepad or your program.</li></ol><p>Won’t connect: disconnect it from any other device, choose “Forget device” on the phone/PC and pair again.</p>',
                ],
            ],
            [
                'connect-24g-dongle',
                ['uz' => "2.4G USB adapter (dongle) bilan juftlash", 'ru' => 'Сопряжение с USB-адаптером 2.4G (донглом)', 'en' => 'Pairing with the 2.4G USB dongle'],
                ['uz' => "2.4g, dongle, adapter, usb priyomnik, simsiz, juftlash, ulanmayapti", 'ru' => '2.4g, донгл, адаптер, usb приёмник, радиоканал, беспроводной, сопряжение, не подключается', 'en' => '2.4g, dongle, receiver, usb adapter, wireless, pairing, not connecting'],
                [
                    'uz' => "<ol><li>Adapterni kompyuterning USB portiga ulang (USB-hub orqali emas).</li><li>Qo'llanmadagi «2.4G mode» kodini, keyin «Pair with dongle» kodini skanerlang.</li><li>Bir necha soniya ichida skaner signal beradi — juftlash tugadi.</li><li>Ishlash masofasi ochiq joyda modelga qarab 50–100 m gacha; devorlar masofani kamaytiradi.</li></ol><p>Adapter yo'qolsa, yangisini sotuvchidan so'rang va qaytadan juftlang.</p>",
                    'ru' => '<ol><li>Вставьте адаптер прямо в USB-порт компьютера (не через USB-хаб).</li><li>Отсканируйте из руководства код «2.4G mode», затем «Pair with dongle».</li><li>Через несколько секунд сканер подаст сигнал — сопряжение завершено.</li><li>Дальность на открытом пространстве — до 50–100 м в зависимости от модели; стены уменьшают дальность.</li></ol><p>Если адаптер утерян — запросите новый у продавца и выполните сопряжение заново.</p>',
                    'en' => '<ol><li>Plug the dongle straight into the computer’s USB port (not through a hub).</li><li>Scan the “2.4G mode” code from the manual, then “Pair with dongle”.</li><li>Within a few seconds the scanner beeps — pairing is done.</li><li>Range in open space is up to 50–100 m depending on the model; walls reduce it.</li></ol><p>Lost the dongle? Ask your reseller for a new one and pair again.</p>',
                ],
            ],
            [
                'factory-reset',
                ['uz' => "Zavod sozlamalarini tiklash (reset)", 'ru' => 'Сброс к заводским настройкам', 'en' => 'Restoring factory defaults (reset)'],
                ['uz' => "reset, zavod sozlamalari, tiklash, standart, ishlamay qoldi, sozlama buzildi", 'ru' => 'сброс, заводские настройки, reset, по умолчанию, перестал работать, сбились настройки', 'en' => 'reset, factory defaults, restore, default settings, stopped working, settings broken'],
                [
                    'uz' => "<p>Skaner g'alati ishlay boshlasa yoki noto'g'ri kod skanerlangan bo'lsa, avval zavod sozlamalarini tiklang.</p><ol><li>Qo'llanmaning birinchi sahifalaridagi «Restore factory defaults» kodini skanerlang.</li><li>Skaner signal beradi va qayta ishga tushadi.</li><li>Kerakli sozlamalarni (Enter, ulanish rejimi) qaytadan o'rnating.</li></ol><p>Diqqat: tiklashdan keyin Bluetooth/2.4G juftlash ham o'chishi mumkin.</p>",
                    'ru' => '<p>Если сканер ведёт себя странно или был отсканирован неверный настроечный код — сначала сбросьте настройки.</p><ol><li>Отсканируйте код «Restore factory defaults» (обычно на первых страницах руководства).</li><li>Сканер подаст сигнал и перезапустится.</li><li>Заново задайте нужные настройки (Enter, режим подключения).</li></ol><p>Внимание: после сброса может слететь сопряжение Bluetooth/2.4G.</p>',
                    'en' => '<p>If the scanner behaves oddly or a wrong setting code was scanned, reset it first.</p><ol><li>Scan the “Restore factory defaults” code (usually in the first pages of the manual).</li><li>The scanner beeps and restarts.</li><li>Set your options again (Enter suffix, connection mode).</li></ol><p>Note: a reset may also clear Bluetooth / 2.4G pairing.</p>',
                ],
            ],
            [
                'cannot-read-qr-screen',
                ['uz' => "Skaner QR kodni yoki telefon ekranidagi kodni o'qimaydi", 'ru' => 'Сканер не читает QR-код или код с экрана телефона', 'en' => 'Scanner does not read QR codes or codes on a phone screen'],
                ['uz' => "qr, ekran, telefon, o'qimaydi, click, payme, 2d, datamatrix, skanerlamaydi", 'ru' => 'qr, экран, телефон, не читает, не сканирует, click, payme, 2d, datamatrix', 'en' => 'qr, screen, phone, not reading, does not scan, 2d, datamatrix, payment code'],
                [
                    'uz' => "<ol><li>Modelingiz turini tekshiring: 1D (lazer yoki CCD) skanerlar faqat chiziqli shtrix-kodlarni o'qiydi — QR va DataMatrix uchun 2D skaner kerak.</li><li>2D skanerda: telefon ekrani yorqinligini oshiring va kodni 10–20 sm masofada ushlang.</li><li>Qalin himoya oynasi yoki yaltiroq plyonka xalaqit berishi mumkin — burchakni biroz o'zgartiring.</li><li>QR kodlar sozlamalarda o'chirilgan bo'lishi mumkin: zavod sozlamalarini tiklang.</li></ol><p>2D skaner tanlashda yordam kerak bo'lsa — botda «Skaner tanlash» bo'limiga yozing.</p>",
                    'ru' => '<ol><li>Проверьте тип модели: 1D-сканеры (лазерные и CCD) читают только линейные штрих-коды — для QR и DataMatrix нужен 2D-сканер.</li><li>Для 2D-сканера: увеличьте яркость экрана телефона и держите код на расстоянии 10–20 см.</li><li>Толстое защитное стекло или глянцевая плёнка дают блики — слегка измените угол.</li><li>Чтение QR могло быть отключено в настройках — выполните сброс к заводским.</li></ol><p>Нужна помощь с выбором 2D-сканера — напишите в разделе «Подобрать сканер».</p>',
                    'en' => '<ol><li>Check the model type: 1D scanners (laser and CCD) read only linear barcodes — QR and DataMatrix need a 2D scanner.</li><li>With a 2D scanner: raise the phone screen brightness and hold the code 10–20 cm away.</li><li>A thick screen protector or glossy film causes glare — tilt the phone slightly.</li><li>QR reading may have been disabled in settings — restore factory defaults.</li></ol><p>Need help choosing a 2D scanner? Use “Choose a scanner” in the bot.</p>',
                ],
            ],
            [
                'asl-belgisi-datamatrix',
                ['uz' => "«Asl belgisi» markirovkasi (DataMatrix) uchun skaner sozlash", 'ru' => 'Сканер для маркировки «Asl belgisi» (DataMatrix)', 'en' => 'Scanner setup for “Asl belgisi” marking codes (DataMatrix)'],
                ['uz' => "asl belgisi, markirovka, datamatrix, gs1, gs belgisi, dori, tamaki, ichimlik, kassa", 'ru' => 'asl belgisi, маркировка, datamatrix, gs1, символ gs, лекарства, табак, напитки, касса', 'en' => 'asl belgisi, marking, datamatrix, gs1, gs separator, pharmacy, tobacco, drinks, cash register'],
                [
                    'uz' => "<p>O'zbekistondagi raqamli markirovka (dorilar, tamaki, ichimliklar va boshqalar) DataMatrix kodlaridan foydalanadi.</p><ol><li>Faqat 2D skaner kerak — 1D lazer/CCD skanerlar bu kodlarni o'qimaydi.</li><li>Kassa dasturi GS (ASCII 29) ajratuvchi belgisini kutadi. Qo'llanmadagi «Transmit GS / FNC1» kodini yoqing.</li><li>Agar dastur kodni qabul qilmasa: klaviatura tilini EN qiling yoki «Alt+Numpad» rejimini yoqing (GS belgisi yo'qolmasligi uchun).</li><li>Ba'zi kassa dasturlari USB-COM (virtual COM port) rejimini talab qiladi — dastur yo'riqnomasini tekshiring.</li></ol>",
                    'ru' => '<p>Цифровая маркировка в Узбекистане (лекарства, табак, напитки и др.) использует коды DataMatrix.</p><ol><li>Нужен только 2D-сканер — 1D лазерные/CCD сканеры эти коды не читают.</li><li>Кассовая программа ожидает разделитель GS (ASCII 29). Включите из руководства код «Transmit GS / FNC1».</li><li>Если программа не принимает код: переключите раскладку на EN или включите режим «Alt+Numpad», чтобы символ GS не терялся.</li><li>Некоторые кассовые программы требуют режим USB-COM (виртуальный COM-порт) — уточните в инструкции программы.</li></ol>',
                    'en' => '<p>Uzbekistan’s digital marking (medicines, tobacco, drinks, etc.) uses DataMatrix codes.</p><ol><li>Only a 2D scanner works — 1D laser/CCD scanners cannot read these codes.</li><li>The POS software expects the GS separator (ASCII 29). Enable “Transmit GS / FNC1” from the manual.</li><li>If the software rejects the code: switch the keyboard layout to EN or enable “Alt+Numpad” mode so the GS character is not lost.</li><li>Some POS programs require USB-COM (virtual COM port) mode — check the program’s instructions.</li></ol>',
                ],
            ],
            [
                'usb-hid-vs-com',
                ['uz' => "USB-HID (klaviatura) va USB-COM (virtual COM port) rejimlari", 'ru' => 'Режимы USB-HID (клавиатура) и USB-COM (виртуальный COM-порт)', 'en' => 'USB-HID (keyboard) vs USB-COM (virtual COM port) modes'],
                ['uz' => "usb com, com port, virtual com, hid, 1c, kassa dasturi, drayver, rs232, rejim", 'ru' => 'usb com, com порт, виртуальный com, hid, 1с, кассовая программа, драйвер, rs232, режим', 'en' => 'usb com, com port, virtual com, vcp, hid, 1c, pos software, driver, rs232, mode'],
                [
                    'uz' => "<ul><li><b>USB-HID</b> (standart): skaner klaviatura kabi kod yozadi, drayver kerak emas. Ko'p dasturlar uchun mos.</li><li><b>USB-COM</b>: dastur kodni COM port orqali oladi (1C, ba'zi kassa dasturlari). Kursor qayerda bo'lishidan qat'i nazar ishlaydi.</li></ul><p>COM rejimiga o'tish: qo'llanmadagi «USB COM / USB VCP» kodini skanerlang. Windows'da «Qurilmalar dispetcheri → Portlar (COM)» bo'limida port raqamini ko'ring va uni dasturda ko'rsating. Kerak bo'lsa, drayverni menejerdan so'rang.</p>",
                    'ru' => '<ul><li><b>USB-HID</b> (по умолчанию): сканер «печатает» код как клавиатура, драйвер не нужен. Подходит для большинства программ.</li><li><b>USB-COM</b>: программа получает код через COM-порт (1С, часть кассовых программ). Работает независимо от положения курсора.</li></ul><p>Переключение: отсканируйте из руководства код «USB COM / USB VCP». В Windows посмотрите номер порта в «Диспетчер устройств → Порты (COM)» и укажите его в программе. При необходимости запросите драйвер у менеджера.</p>',
                    'en' => '<ul><li><b>USB-HID</b> (default): the scanner “types” the code like a keyboard, no driver needed. Fits most software.</li><li><b>USB-COM</b>: the program reads the code from a COM port (1C, some POS software). Works regardless of cursor position.</li></ul><p>To switch, scan “USB COM / USB VCP” from the manual. In Windows, find the port number under “Device Manager → Ports (COM)” and set it in your program. Ask a manager for the driver if needed.</p>',
                ],
            ],
            [
                'beeps-no-data',
                ['uz' => "Skaner signal beradi, lekin kod ekranda chiqmaydi", 'ru' => 'Сканер пищит, но код не появляется', 'en' => 'Scanner beeps but no code appears'],
                ['uz' => "signal beradi, chiqmaydi, ma'lumot kelmaydi, yozmayapti, kursor, ulanmagan", 'ru' => 'пищит, не появляется, не вводит, данные не приходят, курсор, не печатает', 'en' => 'beeps, nothing appears, no data, not typing, cursor, no input'],
                [
                    'uz' => "<ol><li>Kursor kiritish maydonida turganini tekshiring (masalan, bloknotda).</li><li>Simsiz skanerda: ehtimol, u «inventarizatsiya» (xotiraga saqlash) rejimida yoki adapter/Bluetooth bilan ulanmagan — qayta juftlang.</li><li>Skaner USB-COM rejimida bo'lsa, kod klaviatura kabi chiqmaydi — USB-HID rejimini yoqing.</li><li>Boshqa USB port yoki kabel bilan sinab ko'ring.</li><li>Yordam bermasa — zavod sozlamalarini tiklang.</li></ol>",
                    'ru' => '<ol><li>Проверьте, что курсор стоит в поле ввода (например, в Блокноте).</li><li>Беспроводной сканер: возможно, он в режиме «инвентаризации» (сохраняет в память) или не связан с адаптером/Bluetooth — выполните сопряжение заново.</li><li>Если сканер в режиме USB-COM, код не «печатается» как с клавиатуры — включите режим USB-HID.</li><li>Попробуйте другой USB-порт или кабель.</li><li>Не помогло — сбросьте к заводским настройкам.</li></ol>',
                    'en' => '<ol><li>Make sure the cursor is in an input field (e.g. Notepad).</li><li>Wireless scanner: it may be in “inventory” (store-to-memory) mode or not linked to the dongle/Bluetooth — pair it again.</li><li>If the scanner is in USB-COM mode, codes are not typed like a keyboard — switch to USB-HID.</li><li>Try another USB port or cable.</li><li>Still nothing — restore factory defaults.</li></ol>',
                ],
            ],
            [
                'inventory-mode-upload',
                ['uz' => "Inventarizatsiya (oflayn) rejimi va saqlangan kodlarni yuklash", 'ru' => 'Режим инвентаризации (офлайн) и выгрузка сохранённых кодов', 'en' => 'Inventory (offline) mode and uploading stored codes'],
                ['uz' => "inventarizatsiya, oflayn, xotira, saqlash, yuklash, ombor, uzoqda skanerlash", 'ru' => 'инвентаризация, офлайн, память, сохранение, выгрузка, склад, вне зоны', 'en' => 'inventory, offline, memory, storage, upload, warehouse, out of range'],
                [
                    'uz' => "<p>Simsiz skanerlar kompyuterdan uzoqda kodlarni xotirada saqlay oladi.</p><ol><li>Qo'llanmadagi «Inventory mode» kodini skanerlang.</li><li>Kerakli tovarlarni skanerlang — kodlar skaner xotirasida saqlanadi.</li><li>Kompyuter yoniga qayting, bloknot yoki dasturni oching va «Upload data» kodini skanerlang.</li><li>Yuklangandan keyin «Clear data» kodi bilan xotirani tozalang.</li><li>Oddiy ishga qaytish: «Normal mode» kodini skanerlang.</li></ol>",
                    'ru' => '<p>Беспроводные сканеры умеют сохранять коды в память вдали от компьютера.</p><ol><li>Отсканируйте из руководства код «Inventory mode».</li><li>Сканируйте товары — коды сохраняются в памяти сканера.</li><li>Вернитесь к компьютеру, откройте Блокнот или программу и отсканируйте «Upload data».</li><li>После выгрузки очистите память кодом «Clear data».</li><li>Возврат в обычный режим — код «Normal mode».</li></ol>',
                    'en' => '<p>Wireless scanners can store codes in memory while out of range.</p><ol><li>Scan “Inventory mode” from the manual.</li><li>Scan your items — codes are stored in the scanner.</li><li>Back at the computer, open Notepad or your program and scan “Upload data”.</li><li>After uploading, clear the memory with “Clear data”.</li><li>Back to normal operation: scan “Normal mode”.</li></ol>',
                ],
            ],
            [
                'battery-charging',
                ['uz' => "Batareya, zaryadlash va uyqu rejimi", 'ru' => 'Аккумулятор, зарядка и спящий режим', 'en' => 'Battery, charging and sleep mode'],
                ['uz' => "batareya, zaryad, zaryadlanmaydi, tez tugaydi, o'chib qoladi, uyqu, kredl", 'ru' => 'аккумулятор, батарея, зарядка, не заряжается, быстро садится, выключается, сон, кредл', 'en' => 'battery, charging, not charging, drains fast, turns off, sleep, cradle'],
                [
                    'uz' => "<ol><li>Birinchi marta 4–6 soat to'liq zaryadlang.</li><li>Asl kabel yoki kredl (baza) va kompyuter USB porti yoki 5V/1A adapterdan foydalaning.</li><li>Skaner tez-tez «o'chib qolsa» — bu uyqu rejimi; tugmani bosing. Uyqu vaqtini qo'llanmadagi «Sleep time» kodlari bilan uzaytirish mumkin.</li><li>Zaryadlanmasa: boshqa kabel/port bilan tekshiring, kredl kontaktlarini tozalang.</li><li>Batareya ishlamay qolsa — servis markaziga murojaat qiling (botda «Qayerdan sotib olish»).</li></ol>",
                    'ru' => '<ol><li>Перед первым использованием зарядите полностью — 4–6 часов.</li><li>Используйте родной кабель или кредл (базу) и USB-порт компьютера либо адаптер 5V/1A.</li><li>Сканер «выключается» — это спящий режим; нажмите курок. Время до сна можно увеличить кодами «Sleep time» из руководства.</li><li>Не заряжается: проверьте другой кабель/порт, очистите контакты кредла.</li><li>Аккумулятор не держит заряд — обратитесь в сервисный центр (в боте «Где купить»).</li></ol>',
                    'en' => '<ol><li>Fully charge before first use — 4–6 hours.</li><li>Use the original cable or cradle and a computer USB port or a 5V/1A adapter.</li><li>The scanner “turns off” — that is sleep mode; press the trigger. Extend the sleep time with the “Sleep time” codes in the manual.</li><li>Not charging: try another cable/port, clean the cradle contacts.</li><li>Battery no longer holds a charge — contact a service center (“Where to buy” in the bot).</li></ol>',
                ],
            ],
            [
                'warranty-service',
                ['uz' => "Kafolat va servis xizmati", 'ru' => 'Гарантия и сервисное обслуживание', 'en' => 'Warranty and service'],
                ['uz' => "kafolat, garantiya, ta'mirlash, servis, buzildi, almashtirish, servis markazi", 'ru' => 'гарантия, ремонт, сервис, сломался, замена, сервисный центр', 'en' => 'warranty, repair, service, broken, replacement, service center'],
                [
                    'uz' => "<p>HENEX skanerlariga rasmiy distribyutor tomonidan kafolat beriladi (muddati modelga bog'liq — sotuvchidan so'rang).</p><ol><li>Murojaat qilishdan oldin zavod sozlamalarini tiklab ko'ring — ko'p muammolar shunday hal bo'ladi.</li><li>Xarid cheki yoki hujjatini va skanerning seriya raqamini tayyorlang.</li><li>Eng yaqin servis markazini botdagi «Qayerdan sotib olish» bo'limidan toping yoki «Menejerga yuborish» tugmasini bosing.</li></ol><p>Kafolat mexanik shikast, suv tegishi va ruxsatsiz ta'mirlashni qamramaydi.</p>",
                    'ru' => '<p>На сканеры HENEX действует гарантия официального дистрибьютора (срок зависит от модели — уточните у продавца).</p><ol><li>Перед обращением попробуйте сброс к заводским настройкам — многие проблемы решаются так.</li><li>Подготовьте чек или документ о покупке и серийный номер сканера.</li><li>Ближайший сервисный центр — в разделе бота «Где купить», либо нажмите «Отправить менеджеру».</li></ol><p>Гарантия не распространяется на механические повреждения, попадание жидкости и неавторизованный ремонт.</p>',
                    'en' => '<p>HENEX scanners carry the official distributor’s warranty (the period depends on the model — ask your reseller).</p><ol><li>Before contacting us, try a factory reset — it solves many issues.</li><li>Have your receipt or purchase document and the scanner’s serial number ready.</li><li>Find the nearest service center under “Where to buy” in the bot, or press “Send to a manager”.</li></ol><p>The warranty does not cover physical damage, liquid damage or unauthorised repair.</p>',
                ],
            ],
        ];
    }
}
