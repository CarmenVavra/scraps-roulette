<x-layouts.app title="Impressum" description="Impressum und Offenlegung von ScrapsRoulette.">
    <x-legal-page kicker="Rechtliches" title="Impressum">
        <p>Informationen und Offenlegung gemäß § 5 E-Commerce-Gesetz (ECG) und § 25 Mediengesetz (MedienG).</p>

        <h2>Medieninhaberin und Herausgeberin</h2>
        <p>
            {{ $legal['name'] }}<br>
            {{ $legal['street'] }}<br>
            {{ $legal['city'] }}<br>
            {{ $legal['country'] }}
        </p>

        <h2>Kontakt</h2>
        <p>E-Mail: <a href="mailto:{{ $legal['email'] }}">{{ $legal['email'] }}</a></p>

        <h2>Grundlegende Richtung (Blattlinie)</h2>
        <p>
            ScrapsRoulette ist ein privates, nicht-kommerzielles Hobbyprojekt. Die Website erzeugt mit Hilfe künstlicher
            Intelligenz humorvolle Rezeptvorschläge aus Lebensmittelresten, um Lebensmittelverschwendung zu reduzieren.
        </p>

        <h2>Haftung für Inhalte</h2>
        <p>
            Die Rezepte und Texte werden automatisch von einer KI (Anthropic Claude) erzeugt und nicht redaktionell geprüft.
            Sie können Fehler enthalten. Prüfe Zutaten vor der Verwendung auf Genießbarkeit, achte auf Allergien und
            Unverträglichkeiten und verwende keine verdorbenen Lebensmittel. Die Nutzung erfolgt auf eigene Verantwortung.
        </p>
        <p>
            Die beleidigenden Kommentare des „Chefkochs“ sind Satire und richten sich gegen die Zutatenauswahl, nicht gegen
            Personen.
        </p>

        <h2>Haftung für Links</h2>
        <p>
            Für Inhalte externer Websites, auf die verlinkt wird, sind ausschließlich deren Betreiber verantwortlich.
            Zum Zeitpunkt der Verlinkung waren keine Rechtsverstöße erkennbar.
        </p>

        <h2>Urheberrecht</h2>
        <p>
            Gestaltung und Programmcode dieser Website sind urheberrechtlich geschützt. Die verwendete Schrift „Archivo“
            steht unter der SIL Open Font License.
        </p>
    </x-legal-page>
</x-layouts.app>
