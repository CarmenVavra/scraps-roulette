<x-layouts.app title="Datenschutz" description="Datenschutzerklärung von ScrapsRoulette.">
    <x-legal-page kicker="Rechtliches" title="Datenschutz­erklärung">
        <p>
            Der Schutz deiner Daten ist uns wichtig. Diese Erklärung informiert dich darüber, welche Daten beim Besuch von
            ScrapsRoulette verarbeitet werden. Grundlage sind die Datenschutz-Grundverordnung (DSGVO), das österreichische
            Datenschutzgesetz (DSG) und das Telekommunikationsgesetz 2021 (TKG 2021).
        </p>

        <h2>1. Verantwortliche Stelle</h2>
        <p>
            {{ $legal['name'] }}<br>
            {{ $legal['street'] }}<br>
            {{ $legal['city'] }}<br>
            {{ $legal['country'] }}<br>
            E-Mail: <a href="mailto:{{ $legal['email'] }}">{{ $legal['email'] }}</a>
        </p>

        <h2>2. Kurz zusammengefasst</h2>
        <ul>
            <li>Kein Benutzerkonto, keine Registrierung, kein Newsletter.</li>
            <li>Keine Analyse- oder Tracking-Tools, keine Werbung, keine Social-Media-Plugins.</li>
            <li>Schriften werden lokal von unserem Server geladen, nicht von Google oder anderen Anbietern.</li>
            <li>Deine eingegebenen Zutaten werden zur Rezepterstellung an Anthropic übermittelt und von uns nicht gespeichert.</li>
        </ul>

        <h2>3. Hosting und Server-Logfiles</h2>
        <p>
            Die Website wird bei der netcup GmbH, Daimlerstraße 25, 76185 Karlsruhe, Deutschland, gehostet. Beim Aufruf der
            Seite verarbeitet der Webserver automatisch technisch notwendige Daten: IP-Adresse, Datum und Uhrzeit, aufgerufene
            Seite, übertragene Datenmenge, Referrer-URL, Browser und Betriebssystem. Diese Daten werden zur Auslieferung der
            Website und zur Abwehr von Angriffen benötigt und nach kurzer Zeit automatisch gelöscht.
        </p>
        <p>
            Rechtsgrundlage ist unser berechtigtes Interesse am sicheren und stabilen Betrieb der Website
            (Art. 6 Abs. 1 lit. f DSGVO). Mit netcup besteht ein Vertrag zur Auftragsverarbeitung gemäß Art. 28 DSGVO.
        </p>

        <h2>4. Cookies</h2>
        <p>
            Wir verwenden ausschließlich technisch notwendige Cookies: ein Sitzungs-Cookie (<code>scrapsroulette-session</code>)
            und ein Sicherheits-Cookie (<code>XSRF-TOKEN</code>) zum Schutz vor gefälschten Anfragen (CSRF). Beide enthalten
            keine Informationen über dich, werden nicht zu Werbe- oder Analysezwecken genutzt und nach spätestens zwei Stunden
            Inaktivität ungültig. Rechtsgrundlage ist § 165 Abs. 3 TKG 2021 sowie Art. 6 Abs. 1 lit. f DSGVO. Eine Einwilligung
            ist dafür nicht erforderlich.
        </p>

        <h2>5. Rezepterstellung mit künstlicher Intelligenz</h2>
        <p>
            Wenn du auf „Chefkoch herausfordern“ klickst, wird deine Zutatenliste an die Anthropic, PBC (San Francisco, USA)
            übermittelt. Dort erzeugt das KI-Modell Claude ({{ $model }}) daraus das Rezept. Übertragen werden nur die von dir
            eingegebenen Zutaten, nicht deine IP-Adresse oder andere Daten über dich. Bitte gib in das Zutatenfeld keine
            persönlichen Informationen ein.
        </p>
        <p>
            Wir selbst speichern weder deine Zutaten noch das erzeugte Rezept. Anthropic verarbeitet die Anfragen laut eigenen
            Angaben, um die Antwort zu erzeugen, speichert sie für begrenzte Zeit zur Missbrauchserkennung und verwendet
            Daten aus der API nicht zum Training seiner Modelle. Rechtsgrundlage ist unser berechtigtes Interesse an der
            Bereitstellung der Funktion, die du aktiv anforderst (Art. 6 Abs. 1 lit. f DSGVO).
        </p>
        <p>
            Die Übermittlung in die USA erfolgt auf Grundlage des Angemessenheitsbeschlusses der EU-Kommission zum
            EU-U.S. Data Privacy Framework bzw. der EU-Standardvertragsklauseln (Art. 46 Abs. 2 lit. c DSGVO). Weitere
            Informationen: <a href="https://www.anthropic.com/legal/privacy" rel="noopener noreferrer" target="_blank">Datenschutzerklärung von Anthropic</a>.
        </p>

        <h2>6. Schutz vor Missbrauch</h2>
        <p>
            Damit die kostenpflichtige KI nicht missbraucht wird, zählen wir die Rezept-Anfragen pro IP-Adresse. Dazu wird ein
            aus der IP-Adresse gebildeter Schlüssel mit einem Zähler höchstens 24 Stunden im Zwischenspeicher des Servers
            abgelegt und danach automatisch gelöscht (Art. 6 Abs. 1 lit. f DSGVO).
        </p>

        <h2>7. Deine Rechte</h2>
        <p>Du hast jederzeit das Recht auf:</p>
        <ul>
            <li>Auskunft über deine gespeicherten Daten (Art. 15 DSGVO),</li>
            <li>Berichtigung (Art. 16 DSGVO) und Löschung (Art. 17 DSGVO),</li>
            <li>Einschränkung der Verarbeitung (Art. 18 DSGVO),</li>
            <li>Datenübertragbarkeit (Art. 20 DSGVO),</li>
            <li>Widerspruch gegen die Verarbeitung (Art. 21 DSGVO).</li>
        </ul>
        <p>
            Wende dich dazu an die oben genannte E-Mail-Adresse. Wenn du glaubst, dass die Verarbeitung deiner Daten gegen das
            Datenschutzrecht verstößt, kannst du dich bei der Aufsichtsbehörde beschweren. In Österreich ist das die
            Datenschutzbehörde, Barichgasse 40–42, 1030 Wien, <a href="https://www.dsb.gv.at" rel="noopener noreferrer" target="_blank">www.dsb.gv.at</a>.
        </p>

        <h2>8. Aktualität</h2>
        <p>Stand: {{ $legal['updated_at'] }}. Wir passen diese Erklärung an, wenn sich die Website oder die Rechtslage ändert.</p>
    </x-legal-page>
</x-layouts.app>
