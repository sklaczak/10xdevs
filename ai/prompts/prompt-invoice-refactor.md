## PROMPT: Refaktoryzacja modułu Invoice do DDD + Hexagonal + CQRS

Jesteś doświadczonym architektem DDD i Software Engineerem. Twoim zadaniem jest przeprowadzić refaktoryzację istniejącego modułu `Invoice` w aplikacji do faktur kosztowych do architektury:
- DDD (bounded context)
- Hexagonal Architecture (ports & adapters)
- CQRS (Command/Query separation)

### Kontekst projektu
- Symfony backend, Twig frontend (UI pozostaje bez zmian funkcjonalnych).
- Konteksty `Person` i `Identity` są już/mają być przeniesione do DDD/CQRS i stanowią wzorzec.
- Moduł `Invoice` jest legacy i ma zostać zrefaktorowany w tej iteracji.
- Refaktor nie może zmieniać zachowania biznesowego (zero feature loss).
- Nie wynosimy logiki domenowej do kontrolerów ani do Doctrine encji infrastrukturalnych.

### Twoje wejście
Masz dostęp do kodu repozytorium. Najpierw zidentyfikuj:
1) istniejące encje/rekordy DB dot. faktur (tabele, pola, relacje),
2) kontrolery i endpointy związane z fakturami,
3) serwisy/klasy z logiką faktur,
4) szablony Twig, które konsumują dane faktur,
5) integracje (np. upload plików PDF, storage, eksport, walidacje).

### Wynik końcowy ma zawierać
1) proponowany bounded context `Invoice` (lub `Billing/Invoice` – zdecyduj),
2) strukturę katalogów zgodną z DDD + Hexagonal + CQRS,
3) listę przypadków użycia (use cases) jako:
    - Commands (zmiana stanu)
    - Queries (odczyt)
4) definicje portów (interfejsy) i adapterów infrastruktury,
5) propozycję encji domenowych, VO i reguł (invariantów),
6) mapping: skąd -> dokąd migrują klasy z legacy,
7) plan testów (unit/integration/functional) i kryteria akceptacji.

### Wymagania architektoniczne (twarde)
- `Domain` nie zna: Symfony, Doctrine, Request/Response, Twig.
- `Application` nie zna: Doctrine i HTTP. Zna porty i DTO.
- `Infrastructure` implementuje porty i integruje się z DB/Framework.
- CQRS: Commands nie zwracają danych domenowych; Queries zwracają DTO.
- Kontrolery są cienkie (mapowanie IO + wywołanie Command/Query).
- Read Model dla Queries może omijać encje domenowe (DTO z SQL/Doctrine QueryBuilder).

### Oczekiwana struktura katalogów (dopasuj do kodu)
Zaprezentuj finalną strukturę katalogów np.:

/src/Invoice
    /Domain
        /Entity
        /ValueObject
        /Policy (opcjonalnie)
        /Repository (porty domenowe)
        /Exception
    /Application
        /Command
        /Query
        /Port
            InvoiceReadModel.php
            Clock.php
    /Infrastructure
        /Persistence
            DoctrineInvoiceRepository.php
        /ReadModel
            DoctrineInvoiceReadModel.php
        /Controller
            InvoiceController.php
        /FileStorage 
            LocalFileStorageAdapter.php

### Use cases – minimalny zestaw (jeśli istnieją w kodzie)
Zidentyfikuj w kodzie i odwzoruj w CQRS (nazwa + payload):
- CreateInvoiceCommand (np. numer, data, dostawca, kwota, waluta, personId)
- UpdateInvoiceCommand (zmiana pól)
- ApproveInvoiceCommand / RejectInvoiceCommand (jeśli istnieją statusy)
- AttachInvoiceFileCommand (jeśli istnieje upload)
- DeleteInvoiceCommand (jeśli istnieje w UI)

Queries:
- GetInvoiceListQuery (filtry/paginacja zgodnie z UI)
- GetInvoiceDetailsQuery (widok szczegółów)
- GetInvoiceForEditQuery (jeśli UI ma osobny formularz)

### Reguły domenowe (przykłady – potwierdź z kodu)
- InvoiceNumber musi być unikalny w ramach dostawcy lub globalnie (sprawdź)
- Kwota > 0
- Statusy (DRAFT/SUBMITTED/APPROVED/REJECTED) – jeśli występują
- Zmiany dozwolone tylko w określonych statusach
- Powiązanie z Person (jeśli istnieje)

### Autoryzacja (spójna z Identity)
- określ, które operacje Invoice wymagają ROLE_ADMIN,
- reszta dla ROLE_USER (jeśli tak jest w obecnym zachowaniu),
- nie zmieniaj dotychczasowych zasad dostępu.

### Format odpowiedzi
Zwróć:
1) diagnozę as-is (co jest w legacy i gdzie),
2) projekt docelowy (struktura + odpowiedzialności klas),
3) lista use cases (commands/queries) + krótkie opisy,
4) porty/adapters: lista interfejsów i implementacji,
5) plan migracji: krok po kroku, minimalizując ryzyko regresji,
6) plan testów + kryteria akceptacji.

### Dodatkowe ograniczenia
- Unikaj „Big Bang”. Zaproponuj podejście iteracyjne:
    - najpierw Queries (read model),
    - potem Commands (write),
    - na końcu usuwanie legacy.
- Jeśli coś jest niejasne w kodzie, opisz założenia i zaznacz punkty do weryfikacji.

START.
