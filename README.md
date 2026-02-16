
# Football World Cup Score Board (PHP)

This project is an in-memory implementation of the Sportradar backend exercise.  
It manages live football games and provides a scoreboard summary sorted by the rules from the task.  
The business logic is framework-independent, and a small Symfony Console entry point is included for execution.

The focus is on clean domain logic, predictable behavior, and easy extension.

---

## Problem

Implement a live score board that supports:

1. Starting a game with initial score `0 - 0`
2. Finishing a game (removes it from the board)
3. Updating the score of an existing game
4. Returning a summary sorted by:
   - Total score (highest first)
   - For ties: most recently started game first

All storage is in memory only.

---

## Usage

- **Install dependencies**

```bash
composer install
```

- **Run demo**

```bash
php bin/console app:scoreboard:demo
```

- **Run the tests**

```bash
vendor/bin/phpunit
```

---

## Example Output

```
Football World Cup Score Board Summary:
1. Uruguay 6 - Italy 6
2. Spain 10 - Brazil 2
3. Mexico 0 - Canada 5
4. Argentina 3 - Australia 1
5. Germany 2 - France 2
```

---

## Project Structure

**Domain model**

- `src/Domain/Model/Game.php`  
  Holds game state and validates team names / score updates.

- `src/Domain/Model/GameSummary.php`  
  Immutable summary model returned to callers.

- `src/Domain/Model/TeamName.php`  
  Centralizes team name handling: `normalizeForDisplay` (trim, reject empty) for display and storage; `normalize` (trim, lowercase) for identity and lookups.

**Service layer**

- `src/Domain/Service/ScoreboardInterface.php`  
  Defines scoreboard operations.

- `src/Domain/Service/Scoreboard.php`  
  In-memory implementation for game lifecycle and summary sorting.

**Symfony console integration**

- `src/Application/Command/ScoreboardDemoCommand.php`  
  Symfony command that executes the assignment sample scenario.

- `bin/console`  
  Minimal console bootstrap using Symfony Console + DI components.

**Domain exceptions**

- `src/Domain/Exception/*`  
  Explicit exceptions for invalid teams, invalid scores, duplicate games, and missing games.

**Tests**

- `tests/Model/GameTest.php`  
- `tests/Model/GameSummaryTest.php`  
- `tests/Service/ScoreboardTest.php`  
- `tests/Application/Command/ScoreboardDemoCommandTest.php`  

---

## Approach

- The scoreboard keeps active games in memory and tracks game start order to support tie-breaking.
- Input normalization is applied for game identity lookups (trim + case-insensitive comparison), so lookups are consistent.
- Summary output is returned as immutable objects to avoid accidental mutation of internal state from outside the service.
- Symfony is used as an execution layer, while domain rules stay decoupled from framework code.
- All business logic is covered by unit tests, including edge cases and the demo scenario.

---

## Edge Cases Handled

- Empty or whitespace team names
- Same team used for home and away (case-insensitive)
- Negative scores
- Duplicate game start attempts
- Finish/update for a game that does not exist
- Case-insensitive and trimmed lookups (`" mexico "` and `"MEXICO"` refer to the same team identity)
- Reversed fixtures treated as different games (`Mexico vs Canada` and `Canada vs Mexico`)

---

## Extending the Solution

If requirements grow (for example persistence, API, or additional ordering rules), the current structure allows incremental changes:

1. Keep domain rules in model/service classes
2. Add new service implementation behind `ScoreboardInterface`
3. Keep tests around behavior and add cases for new rules
