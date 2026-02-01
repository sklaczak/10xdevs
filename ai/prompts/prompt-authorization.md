## PROMPT: Refaktor autoryzacji i uwierzytelniania w App\Identity (DDD + Hex + CQRS)

Jesteś doświadczonym architektem DDD i security-minded backend developerem. Twoim zadaniem jest uporządkować autoryzację i uwierzytelnianie w aplikacji Symfony, w kontekście `App\Identity`, zgodnie z:

- DDD (bounded context Identity)
- Hexagonal Architecture (Ports & Adapters)
- CQRS (Command / Query separation)
- zasadami bezpieczeństwa (brak wycieków informacji, minimalny atak surface)

### Kontekst projektu
- Backend: Symfony
- Frontend: Twig (bez zmiany technologii)
- `Person` jest osobnym kontekstem (DDD) i jest źródłem prawdy dla dopuszczenia do rejestracji.
- W tej iteracji aktywna jest tylko metoda: login + hasło.
- Inne metody (OAuth2/SSO) mają być opisane jako możliwe adaptery, ale bez implementacji.

### Wymagania biznesowe (twarde)
1) Logowanie: email + hasło.
2) Rejestracja: możliwa wyłącznie jeśli email istnieje w tabeli `person` i osoba jest aktywna.
3) Role: co najmniej `ROLE_USER` i `ROLE_ADMIN`.
4) Autoryzacja endpointów: użytkownik bez ADMIN nie ma dostępu do panelu administracyjnego.
5) Błędy rejestracji/logowania: komunikaty nie mogą ujawniać czy email istnieje (anti-enumeration).
6) Refaktor nie może zmienić zachowania aplikacji poza:
    - uporządkowaniem struktury,
    - domknięciem luk bezpieczeństwa,
    - wprowadzeniem jawnych granic odpowiedzialności.

### Twoje wejście
Masz dostęp do repozytorium i kodu w przestrzeni nazw `App\Identity` oraz integracje w security config. Najpierw:
- wskaż gdzie są kontrolery logowania/rejestracji,
- wskaż encje związane z użytkownikiem/rolami,
- wskaż jak jest realizowane hashowanie i weryfikacja hasła,
- wskaż jak są sprawdzane role (Symfony voters/attributes/security.yaml),
- wskaż przepływ rejestracji i zależność do Person (jeśli istnieje lub trzeba dodać).

### Output – co masz dostarczyć
Zwróć kompletną propozycję:
1) struktury katalogów kontekstu Identity w stylu DDD/Hex/CQRS,
2) listy przypadków użycia (use cases) jako Commands i Queries,
3) portów i adapterów (interfejsy + implementacje infrastruktury),
4) rozdzielenia odpowiedzialności klas,
5) planu migracji (iteracyjnie, minimalizując regresję),
6) planu testów (unit/integration/functional/security) + kryteriów akceptacji.

### Docelowa struktura katalogów (dostosuj do kodu)
Zaproponuj strukturę np.:

/src/Identity
    /Domain
        /Entity
            UserAccount.php
            Role.php
        /ValueObject
            Email.php
            PasswordHash.php
            UserId.php
        /Policy
            PasswordPolicy.php
        /Repository
            UserAccountRepository.php
            RoleRepository.php
        /Exception
            AuthenticationFailed.php
            RegistrationNotAllowed.php
    /Application
        /Command
            RegisterUserCommand.php
            RegisterUserHandler.php
            ChangePasswordCommand.php
        /Query
            GetUserByEmailQuery.php
            GetUserRolesQuery.php
        /Port
            PasswordHasher.php
            Authenticator.php (jeśli potrzeba)
            PersonEligibilityReadModel.php  
            UserReadModel.php          
    /Infrastructure
        /Security
            SymfonyPasswordHasherAdapter.php
            SymfonyAuthenticatorAdapter.ph
            AccessControlConfig.php
        /Persistence
            DoctrineUserAccountRepository.php
            DoctrineRoleRepository.php
        /ReadModel
            DoctrineUserReadModel.php
            DoctrinePersonEligibilityReadModel.php
        /Controller
            AuthController.php

### CQRS – definicja use case’ów (minimum)
Commands:
- RegisterUserCommand(email, plainPassword)
    - wymaga sprawdzenia PersonEligibilityReadModel (czy email istnieje + osoba aktywna)
    - tworzy UserAccount z ROLE_USER
- ChangePasswordCommand(userId, oldPassword, newPassword) (tylko jeśli istnieje funkcja)
- AssignRoleCommand(userId, role) (tylko jeśli jest w systemie admina)

Queries:
- GetUserByEmailQuery(email) -> UserDTO (do logowania / walidacji)
- GetUserProfileQuery(userId) -> ProfileDTO (jeśli istnieje widok profilu)
- GetAccessMatrixQuery() (opcjonalnie, jeśli jest panel admina z uprawnieniami)

### Port do Person (twardy wymóg)
W Identity ma istnieć port:
- PersonEligibilityReadModel
    - method: isEligibleForRegistration(email): bool
    - NIE zwraca przyczyn (tylko true/false), żeby ograniczyć wycieki informacji.
      Infrastructure implementuje go przez odczyt z bazy `person` (Read Model), bez domeny Person w Identity.

### Bezpieczeństwo – wymagania implementacyjne
- hasła przechowywane wyłącznie jako hash (Symfony PasswordHasher),
- brak logowania/wyświetlania plainPassword,
- anty-enumeration:
    - logowanie: jeden komunikat dla „zły email lub hasło”
    - rejestracja: jeden komunikat dla „nie można utworzyć konta”
- ograniczenie bruteforce (jeśli jest w kodzie/frameworku – opisz i zachowaj),
- kontrolery bez logiki: mapują Request -> Command/Query -> Response.

### Autoryzacja dostępu (roles)
Zidentyfikuj wszystkie route’y wymagające ADMIN i:
- zaproponuj jedną strategię:
    - atrybuty #[IsGranted] / security.yaml access_control / Voter
- opisz wybór i konsekwencje.
  Nie zmieniaj zachowania dostępu, ale uczyń je jednoznacznym i testowalnym.

### Plan migracji (iteracyjny, bez Big Bang)
Wymagam planu w krokach:
1) wydzielenie portów + read models
2) przeniesienie logiki logowania/rejestracji do Application Commands/Queries
3) adaptery infrastruktury (Doctrine + Symfony Security)
4) uproszczenie kontrolerów
5) usunięcie/wyłączenie legacy kodu

### Testy (SDET)
Zaproponuj testy:
- Unit (Domain: VO, reguły; Application: Handlery z mockami portów)
- Integration (Doctrine repozytoria, hasher adapter, PersonEligibility adapter)
- Functional (HTTP: /login, /register, /admin)
- Security (enumeration, access bypass, brute force – przynajmniej scenariusze)

### Format odpowiedzi
Odpowiedz w sekcjach:
1) As-is analiza (gdzie co jest)
2) Target design (katalogi + odpowiedzialności)
3) Use cases (commands/queries)
4) Porty i adaptery (lista)
5) Plan migracji (kroki)
6) Test plan + kryteria akceptacji

START.
