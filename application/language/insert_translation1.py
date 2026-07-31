import os

# BASE_DIR = r"applications\language"
BASE_DIR = r"d:/privat/xampp/htdocs/github/cn-tools/easyappointments/application"

LANGUAGES = {
    "english": "Reschedule / Cancel Appointment",
    "german": "Termin verschieben / stornieren",
    "french": "Reprogrammer / annuler le rendez-vous",
    "italian": "Reprogrammare / annullare appuntamento",
    "spanish": "Reprogramar / cancelar cita",
    "dutch": "Afspraak verplaatsen / annuleren",
    "danish": "Ombook / annuller aftale",
    "swedish": "Boka om / avboka tid",
    "norwegian": "Endre / avbestille avtale",
    "finnish": "Siirrä / peruuta ajanvaraus",
    "portuguese": "Reagendar / cancelar consulta",
    "portuguese-br": "Reagendar / cancelar agendamento",
    "polish": "Przełóż / anuluj wizytę",
    "czech": "Přeplánovat / zrušit schůzku",
    "slovak": "Preplánovať / zrušiť stretnutie",
    "slovenian": "Prestavi / prekliči termin",
    "croatian": "Promijeni termin / otkaži termin",
    "serbian": "Promeni termin / otkaži termin",
    "bosnian": "Pomjeri termin / otkaži termin",
    "hungarian": "Időpont módosítása / lemondása",
    "romanian": "Reprogramează / anulează programarea",
    "bulgarian": "Пренасрочване / отмяна на час",
    "russian": "Перенести / отменить запись",
    "ukrainian": "Перенести / скасувати запис",
    "greek": "Επαναπρογραμματισμός / ακύρωση ραντεβού",
    "turkish": "Randevuyu yeniden planla / iptal et",
    "arabic": "إعادة جدولة / إلغاء الموعد",
    "hebrew": "שינוי מועד / ביטול פגישה",
    "persian": "تغییر زمان / لغو نوبت",
    "hindi": "अपॉइंटमेंट पुनर्निर्धारित / रद्द करें",
    "marathi": "अपॉइंटमेंट पुनर्नियोजित करा / रद्द करा",
    "japanese": "予約の変更 / キャンセル",
    "chinese": "重新安排 / 取消预约",
    "traditional-chinese": "重新安排 / 取消預約",
    "thai": "เลื่อนนัดหมาย / ยกเลิกนัดหมาย",
    "estonian": "Muuda / tühista broneering",
    "latvian": "Pārplānot / atcelt vizīti",
    "lithuanian": "Perplanuoti / atšaukti vizitą",
    "luxembourgish": "Rendez-vous verréckelen / annuléieren",
    "catalan": "Reprogramar / cancel·lar cita",
    "albanian": "Riplanifiko / anulo takimin"
}

TARGET_KEY = "appointment_details_was_sent_to_you"
NEW_KEY = "appointment_management_link"

for lang_folder in os.listdir(BASE_DIR):
    file_path = os.path.join(BASE_DIR, lang_folder, "translations_lang.php")

    if not os.path.isfile(file_path):
        continue

    translation = LANGUAGES.get(lang_folder)

    if not translation:
        print(f"Übersetzung fehlt für: {lang_folder}")
        continue

    with open(file_path, "r", encoding="utf-8") as f:
        lines = f.readlines()

    if any(f"$lang['{NEW_KEY}']" in line for line in lines):
        print(f"Bereits vorhanden: {lang_folder}")
        continue

    result = []
    inserted = False

    for line in lines:
        result.append(line)

        if f"$lang['{TARGET_KEY}']" in line and not inserted:
            result.append(
                f"$lang['{NEW_KEY}'] = '{translation}';\n"
            )
            inserted = True

    if inserted:
        with open(file_path, "w", encoding="utf-8") as f:
            f.writelines(result)

        print(f"Aktualisiert: {lang_folder}")
    else:
        print(
            f"Key '{TARGET_KEY}' nicht gefunden in: {lang_folder}"
        )
