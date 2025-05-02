---
layout: psource-theme
title: "CP Smart CRM"
---

<h2 align="center" style="color:#38c2bb;">📚 CP Smart CRM</h2>

<div class="menu">
  <a href="https://github.com/cp-psource/cp-smart-crm/discussions" style="color:#38c2bb;">💬 Forum</a>
  <a href="dokumentation.html" style="color:#38c2bb;">📝 Dokumentation</a>
  <a href="https://github.com/cp-psource/cp-smart-crm/releases" style="color:#38c2bb;">📝 Download</a>
</div>

## 1. Grundeinstellungen

Nach der Aktivierung des Plugins siehst Du eine Benachrichtigung, in der Du einige grundlegende Daten eingeben musst, um das Managementsystem zu verwenden, bis dahin ist die Navigation zwischen den verschiedenen Abschnitten blockiert.

![crm-einrichtung.jpeg](assets/images/crm-einrichtung.jpeg)

## 2. CP Smart CRM-Optionen

Im Menü "DIENSTPROGRAMME"->Einstellungen findest Du als ersten Punkt des Untermenüs die Konfigurationseinstellungen des Managementsystems, die in Abschnitte unterteilt sind:
![cp-smart-crm-optionen.jpeg](assets/images/cp-smart-crm-optionen.jpeg)

### Dokument-Einstellungen

**Einrichtung von Dokumenten**

**GRUNDLEGENDER MEHRWERTSTEUERSATZ**
Die Standardeinstellung des Mehrwertsteuersatzes. Es ist möglich, den Wert der Mehrwertsteuer in den einzelnen Zeilen der ausgestellten Rechnung zu ändern.
Im Falle der Aktivierung des WP Smart CRM WOOcommerce Addon ist der Mehrwertsteuersatz derjenige, der im einzelnen Produkt festgelegt ist (der Standard-Mehrwertsteuerwert der Produkte ist in jedem Fall der in diesem Abschnitt festgelegte Wert

**NUMMERIERUNG DER DOKUMENTE**
Es ist möglich, Präfixe und Suffixe festzulegen, die der Nummerierung von Rechnungen und Kostenvoranschlägen hinzugefügt werden. Es ist auch möglich, eine anfängliche fortlaufende Zahl festzulegen, von der aus mit der Nummerierung von Rechnungen und Kostenvoranschlägen begonnen wird.
ACHTUNG: Es wird nicht empfohlen, diesen Vorgang durchzuführen, nachdem Sie bereits Rechnungen erstellt haben, da dies zu Inkonsistenzen in der Buchhaltung führen kann

**BENACHRICHTIGUNG "MAHNUNG" FÜR DIE ZAHLUNG DER RECHNUNG**
Es ist möglich, standardmäßig eine Anzahl von Tagen nach dem Fälligkeitsdatum der Rechnung festzulegen, um Benachrichtigungen zu senden. Diese Funktion ist nützlich, um Administratoren oder ausgewählte Benutzer daran zu erinnern, zu überprüfen, ob die Rechnung bezahlt wurde. Die Standardeinstellungen können pro Dokument geändert werden


settings-dokumente
Kopf des Dokuments

**AUSRICHTUNG DES DOKUMENTKOPFES**
Mit einem einfachen Drag & Drop ist es möglich, die Ausrichtung der Kopfelemente von Rechnungen und Kostenvoranschlägen zu ändern: Logo und Kopfzeile nach rechts oder links

settings-header-dokumente

**Zahlungsarten**

**DEFINITION DER ZAHLUNGSARTEN**
Zahlungsarten sind Textzeichenfolgen, die im Ausdruck der Rechnung angegeben werden (z.B. 30 Tage Überweisung, 60 Tage Kreditkarte, etc.) und bei der Erstellung von Rechnungen verwendet werden.
Wenn ihnen eine tatsächliche Anzahl von Tagen zugeordnet ist (mit dem zweiten Feld "Tage"), wird dieser Wert verwendet, um automatisch das Fälligkeitsdatum für die Zahlung der Rechnung festzulegen und die (optionalen) entsprechenden Benachrichtigungen festzulegen.

einstellungen-dokumente-zahlungsmethoden

Nachrichteneinstellung in Rechnungen/Belegen

**MELDUNGEN AUF DER RECHNUNG/DEM KOSTENVORANSCHLAG**
In diesem Abschnitt können Sie einige Standardzeichenfolgen festlegen, die beim Drucken von Rechnungen und Kostenvoranschlägen wiederholt werden sollen.
Insbesondere: das Präfix, das an den Namen des Empfängers angehängt werden soll (Liebes Zeichen, liebe Adresse...), ein Freitext, der vor dem automatisch generierten Dokument platziert werden soll, ein Text, der vor dem Dokument platziert wird. Ein Beispiel für einen "Vorher"-Text kann das klassische "Wie vereinbart senden wir unser bestes Angebot für Folgendes:" sein, während ein Beispiel für einen "Nachher"-Text lauten kann: "Wir stehen Ihnen weiterhin für alle Fragen zur Verfügung und bei dieser Gelegenheit bieten wir unsere besten Grüße an"

settings-documents-messages