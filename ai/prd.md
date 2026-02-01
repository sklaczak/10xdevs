# PRD – Refaktor do DDD + Hexagonal Architecture + CQRS

**Cel:** uporządkowanie istniejącego kodu aplikacji poprzez refaktor do DDD, architektury heksagonalnej oraz CQRS  
**Zakres:** wyłącznie kontekst `Person` (pozostałe moduły jako legacy)  
**Typ dokumentu:** Technical PRD / Refactoring Guide

---

## 1. Cel refaktoru

Celem refaktoru jest:

- rozdzielenie logiki domenowej od frameworka,
- eliminacja logiki biznesowej z kontrolerów,
- wprowadzenie jednoznacznych granic kontekstu,
- poprawa testowalności,
- przygotowanie kodu pod dalszy refaktor innych modułów.

Refaktor NIE zmienia zachowania biznesowego aplikacji.

---

## 2. Docelowy model architektoniczny

### 2.1 Zasady ogólne

- domena nie zna frameworka (Symfony, Doctrine),
- Application Layer nie zawiera logiki domenowej,
- Infrastructure jest wymienna,
- komunikacja wejścia/wyjścia tylko przez porty,
- pełne rozdzielenie Command / Query.

---

## 3. Struktura katalogów – kontekst Person

/src/Person
    /Domain
        /Entity
            Person.php
        /ValueObject
            PersonId.php
            Email.php
            PersonStatus.php
        /Repository
            PersonRepository.php
        /Exception
            PersonNotFound.php
            PersonInactive.php
    /Application
        /Command
            ActivatePersonCommand.php
            ActivatePersonHandler.php
        /Query
            GetPersonByEmailQuery.php
            GetPersonByEmailHandler.php
        /Port
            PersonReadModel.php
    /Infrastructure
        /Persistence
            DoctrinePersonRepository.php
        /ReadModel
            DoctrinePersonReadModel.php
        /Controller
            PersonController.php
---

## 4. Warstwa Domain

### 4.1 Odpowiedzialność

Warstwa Domain:
- zawiera **czystą logikę biznesową**,
- NIE zna Doctrine, Symfony ani HTTP,
- NIE wykonuje IO,
- jest w pełni testowalna unitowo.

---

### 4.2 Entity: Person

**Odpowiedzialność:**
- reprezentuje osobę w systemie,
- pilnuje invariantów domenowych.

**Przykładowe reguły:**
- osoba może być aktywna lub nieaktywna,
- email jest niezmienny,
- osoba nieaktywna nie może założyć konta.

Person **NIE**:
- zapisuje się do bazy,
- nie waliduje requestów HTTP.

---

### 4.3 Value Objects

| VO | Odpowiedzialność |
|---|----------------|
| PersonId | jednoznaczna identyfikacja |
| Email | walidacja formatu |
| PersonStatus | ACTIVE / INACTIVE |

---

### 4.4 Repository (port domenowy)

interface PersonRepository


**Odpowiedzialność:**
- abstrakcja dostępu do danych,
- zwraca i zapisuje encje domenowe.

Domain zna tylko **interfejs**.

---

## 5. Warstwa Application

### 5.1 Odpowiedzialność

- orkiestracja przypadków użycia,
- brak logiki domenowej,
- brak zależności od frameworka,
- realizacja CQRS.

---

### 5.2 Commands

Command:
- jest DTO,
- reprezentuje intencję zmiany stanu.

ActivatePersonCommand


Handler:
- pobiera encję z repozytorium,
- wywołuje metody domenowe,
- zapisuje zmiany.

---

### 5.3 Queries

Query:
- DTO wejściowe,
- brak efektów ubocznych.

Handler:
- używa Read Model,
- zwraca DTO pod UI.

---

### 5.4 Read Model (port aplikacyjny)

interface PersonReadModel


**Odpowiedzialność:**
- zoptymalizowany odczyt danych,
- brak encji domenowych,
- brak logiki biznesowej.

---

## 6. Warstwa Infrastructure

### 6.1 Odpowiedzialność

- implementacja portów,
- integracja z Doctrine, Symfony, DB,
- brak logiki domenowej.

---

### 6.2 Persistence

DoctrinePersonRepository


- implementuje `PersonRepository`,
- mapuje encje domenowe na encje Doctrine.

---

### 6.3 Read Model

DoctrinePersonReadModel


- implementuje `PersonReadModel`,
- zwraca DTO,
- używany wyłącznie przez Query Handlery.

---

### 6.4 Controller (Adapter wejściowy)

PersonController


Controller:
- mapuje HTTP → Command / Query,
- NIE zawiera logiki biznesowej,
- NIE zna domeny poza DTO.

---

## 7. Przepływ danych (przykład)

### Use case: sprawdzenie osoby po emailu

1. HTTP Request
2. Controller tworzy `GetPersonByEmailQuery`
3. QueryHandler używa `PersonReadModel`
4. Zwracane jest DTO
5. Controller renderuje Twig

---

## 8. Testowanie

### Domain
- testy invariantów,
- testy Value Objects.

### Application
- testy handlerów (mock repozytoriów).

### Infrastructure
- testy integracyjne Doctrine.

---

## 9. Granica legacy

- Person jest jedynym kontekstem w DDD,
- pozostałe moduły komunikują się przez:
    - Application Layer,
    - bez bezpośredniego dostępu do Domain.

---

## 10. Definition of Done

- brak logiki domenowej poza Domain,
- kontrolery ≤ mapowanie IO,
- Command ≠ Query,
- pełne pokrycie testami Person.
