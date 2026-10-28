Hi Claude! Ich brauche ein UI/UX-Design für ein privates Web-Projekt namens "ScrapsLotto", basierend auf Laravel und Tailwind CSS. 

Das Konzept: Der Nutzer gibt unbegrenzt viele Lebensmittel-Reste ein, die er noch zu Hause hat. Eine KI wählt die besten Zutaten aus und generiert ein Rezept – allerdings im Stil eines arroganten, sarkastischen 5-Sterne-Kochs, der den Nutzer für seine Reste beschimpft, aber am Ende ein geniales Rezept liefert.

Bitte erstelle mir das Interface-Design. Ich benötige sauberen HTML-Code mit modernen Tailwind-CSS-Klassen (gerne als Single-Page-Layout oder aufgeteilt in Eingabe- und Ergebnis-Ansicht), den ich direkt in meine Laravel-Blade-Views einfügen kann.

Folgende Key-Features und Design-Vorgaben müssen enthalten sein:

1. Visueller Stil & Theme:
- Ein moderner, "bistro-artiger" Dark- oder Light-Mode (z. B. edles Anthrazit gemischt mit einem leicht aggressiven Akzent-Rot oder Warn-Orange, passend zum wütenden Koch).
- Spielerischer, aber cleaner Look.

2. Der "Zutaten-Friedhof" (Eingabebereich):
- Ein zentrales, dynamisches Eingabefeld (Input Field) für Zutaten.
- Eingegebene Zutaten sollen sofort darunter als "Tags" / "Badges" erscheinen (mit einem "X" zum Löschen). Da der Nutzer VIELE Reste eingeben kann, müssen sich die Tags elegant in Rastern oder Zeilen anordnen.

3. Der Roulette-Button:
- Ein großer, prominenter Action-Button ("Roulette drehen" oder "Chefkoch herausfordern").
- Der Button sollte visuell so gestaltet sein, dass er sich perfekt für eine spätere CSS-Ladeanimation (z. B. Rotieren oder Pulsieren) eignet, während die KI im Hintergrund arbeitet.

4. Die Zwei-geteilte Ergebnis-Ansicht (Wichtig!):
- Bereich A: "Das Urteil des Chefkochs" -> Eine prominente Box (z. B. im Stil einer wütenden Sprechblase oder mit einer Koch-Emoji-Visualisierung 👨‍🍳💢). Hier steht der sarkastische Rant der KI in auffälliger, leicht chaotischerer Schrift oder Farbe.
- Bereich B: "Die kulinarische Rettung" -> Eine visuell komplett getrennte, sehr cleane, strukturierte "Rezeptkarte". Hier werden die tatsächlich genutzten Zutaten aufgelistet und die Kochschritte (Steps) übersichtlich als Checkliste oder nummerierte Liste dargestellt.

Bitte generiere den HTML/Tailwind-Code so modular wie möglich, sodass ich Livewire oder einfaches JavaScript für die dynamischen Tag-Eingaben und das Einblenden des Ergebnisses leicht anbinden kann.
