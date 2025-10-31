VM/NM/Beides – Minimaler Patch (ohne DB-Änderung)
-------------------------------------------------
• In 'Workshop-Namen' kann neben der URL der Modus stehen: vm | nm | beides
  Format (eine Zeile pro Workshop):
    Name, TT.MM.JJJJ [HH:MM], URL, vm|nm|beides, [Beschreibung]
  Wenn der 4. Wert NICHT vm/nm/beides ist, bleibt er eine Beschreibung (alt-kompatibel).
• Die Ansicht filtert danach, und Buchen auf verbotene Slots wird verhindert.
• Keine Änderungen an DB/Forms nötig. Caches leeren nach Upload.
