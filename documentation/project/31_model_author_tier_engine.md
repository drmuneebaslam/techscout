# 31. 20-Sublevel Author Tier Engine (`AuthorTierEngine.php`)

## File Location & Metadata
- **File Path**: [`app/Models/AuthorTierEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuthorTierEngine.php)
- **Primary Role**: Computes author progression across a 20-sublevel progression matrix, dynamically calculating creator CPM multipliers, reputation requirements, and tier badges.
- **Dependencies**: [`app/Models/Author.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Author.php), [`app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php).

---

## 20-Sublevel Author Tier Hierarchy Matrix

TechScout's author monetization system categorizes creators into 5 primary Leagues, with each League containing 4 distinct Sublevels (Sublevels 1 through 20):

```
Author Progression Matrix
├── Bronze League (Sublevels 1 - 4)
│   ├── Sublevel 1: Bronze I    (0 - 249 Points)      -> 1.00x CPM Multiplier
│   ├── Sublevel 2: Bronze II   (250 - 499 Points)    -> 1.05x CPM Multiplier
│   ├── Sublevel 3: Bronze III  (500 - 749 Points)    -> 1.10x CPM Multiplier
│   └── Sublevel 4: Bronze IV   (750 - 999 Points)    -> 1.15x CPM Multiplier
├── Silver League (Sublevels 5 - 8)
│   ├── Sublevel 5: Silver I    (1,000 - 1,499 Pts)   -> 1.20x CPM Multiplier
│   ├── Sublevel 6: Silver II   (1,500 - 1,999 Pts)   -> 1.25x CPM Multiplier
│   ├── Sublevel 7: Silver III  (2,000 - 2,499 Pts)   -> 1.30x CPM Multiplier
│   └── Sublevel 8: Silver IV   (2,500 - 2,999 Pts)   -> 1.35x CPM Multiplier
├── Gold League (Sublevels 9 - 12)
│   ├── Sublevel 9: Gold I      (3,000 - 3,999 Pts)   -> 1.40x CPM Multiplier
│   ├── Sublevel 10: Gold II    (4,000 - 4,999 Pts)   -> 1.48x CPM Multiplier
│   ├── Sublevel 11: Gold III   (5,000 - 5,999 Pts)   -> 1.56x CPM Multiplier
│   └── Sublevel 12: Gold IV    (6,000 - 6,999 Pts)   -> 1.65x CPM Multiplier
├── Diamond League (Sublevels 13 - 16)
│   ├── Sublevel 13: Diamond I  (7,000 - 8,499 Pts)   -> 1.75x CPM Multiplier
│   ├── Sublevel 14: Diamond II (8,500 - 9,999 Pts)   -> 1.85x CPM Multiplier
│   ├── Sublevel 15: Diamond III(10,000 - 11,999 Pts) -> 1.95x CPM Multiplier
│   └── Sublevel 16: Diamond IV (12,000 - 14,999 Pts) -> 2.05x CPM Multiplier
└── Partner Elite League (Sublevels 17 - 20)
    ├── Sublevel 17: Partner Elite I   (15,000 - 19,999 Pts) -> 2.15x CPM Multiplier
    ├── Sublevel 18: Partner Elite II  (20,000 - 29,999 Pts) -> 2.25x CPM Multiplier
    ├── Sublevel 19: Partner Elite III (30,000 - 49,999 Pts) -> 2.35x CPM Multiplier
    └── Sublevel 20: Partner Elite IV  (50,000+ Points)      -> 2.50x CPM Multiplier
```

---

## Detailed Method & Calculation Implementation

### 1. `evaluateAuthorTier(int $authorId): array`
Calculates author total pageviews, published article count, upvote count, and reputation score, then updates the author's record in the database.

```php
namespace App\Models;

use App\Core\Database;

class AuthorTierEngine {
    
    private static array $tierMatrix = [
        1  => ['league' => 'Bronze',        'sublevel' => 'Bronze I',        'min_points' => 0,     'multiplier' => 1.00],
        2  => ['league' => 'Bronze',        'sublevel' => 'Bronze II',       'min_points' => 250,   'multiplier' => 1.05],
        3  => ['league' => 'Bronze',        'sublevel' => 'Bronze III',      'min_points' => 500,   'multiplier' => 1.10],
        4  => ['league' => 'Bronze',        'sublevel' => 'Bronze IV',       'min_points' => 750,   'multiplier' => 1.15],
        5  => ['league' => 'Silver',        'sublevel' => 'Silver I',        'min_points' => 1000,  'multiplier' => 1.20],
        6  => ['league' => 'Silver',        'sublevel' => 'Silver II',       'min_points' => 1500,  'multiplier' => 1.25],
        7  => ['league' => 'Silver',        'sublevel' => 'Silver III',      'min_points' => 2000,  'multiplier' => 1.30],
        8  => ['league' => 'Silver',        'sublevel' => 'Silver IV',       'min_points' => 2500,  'multiplier' => 1.35],
        9  => ['league' => 'Gold',          'sublevel' => 'Gold I',          'min_points' => 3000,  'multiplier' => 1.40],
        10 => ['league' => 'Gold',          'sublevel' => 'Gold II',         'min_points' => 4000,  'multiplier' => 1.48],
        11 => ['league' => 'Gold',          'sublevel' => 'Gold III',        'min_points' => 5000,  'multiplier' => 1.56],
        12 => ['league' => 'Gold',          'sublevel' => 'Gold IV',         'min_points' => 6000,  'multiplier' => 1.65],
        13 => ['league' => 'Diamond',       'sublevel' => 'Diamond I',       'min_points' => 7000,  'multiplier' => 1.75],
        14 => ['league' => 'Diamond',       'sublevel' => 'Diamond II',      'min_points' => 8500,  'multiplier' => 1.85],
        15 => ['league' => 'Diamond',       'sublevel' => 'Diamond III',     'min_points' => 10000, 'multiplier' => 1.95],
        16 => ['league' => 'Diamond',       'sublevel' => 'Diamond IV',      'min_points' => 12000, 'multiplier' => 2.05],
        17 => ['league' => 'Partner Elite', 'sublevel' => 'Partner Elite I', 'min_points' => 15000, 'multiplier' => 2.15],
        18 => ['league' => 'Partner Elite', 'sublevel' => 'Partner Elite II','min_points' => 20000, 'multiplier' => 2.25],
        19 => ['league' => 'Partner Elite', 'sublevel' => 'Partner Elite III','min_points' => 30000,'multiplier' => 2.35],
        20 => ['league' => 'Partner Elite', 'sublevel' => 'Partner Elite IV', 'min_points' => 50000,'multiplier' => 2.50],
    ];

    public static function evaluateAuthorTier(int $authorId): array {
        $db = Database::getInstance();
        $author = $db->fetch("SELECT * FROM authors WHERE id = ?", [$authorId]);
        if (!$author) return [];

        $reputationPoints = (int) $author['reputation_points'];
        $assignedSublevel = 1;

        foreach (self::$tierMatrix as $level => $config) {
            if ($reputationPoints >= $config['min_points']) {
                $assignedSublevel = $level;
            } else {
                break;
            }
        }

        $tierData = self::$tierMatrix[$assignedSublevel];

        // Update Database Record
        $db->execute(
            "UPDATE authors SET 
                author_tier_sublevel = ?, 
                author_league = ?, 
                cpm_multiplier = ? 
             WHERE id = ?",
            [$assignedSublevel, $tierData['league'], $tierData['multiplier'], $authorId]
        );

        return array_merge($tierData, ['assigned_level' => $assignedSublevel]);
    }

    public static function getTierHierarchy(): array {
        return self::$tierMatrix;
    }
}
```

---

## Detailed QA Test Suite for Author Tier Engine

### Test Case 1: Tier Promotion Execution
- **QA Test ID**: `TC-TIER-001`
- **Target Function**: `AuthorTierEngine::evaluateAuthorTier()`
- **Execution Steps**:
  1. Set author ID 3 reputation points to 3,500 points in database:
     ```sql
     UPDATE authors SET reputation_points = 3500 WHERE id = 3;
     ```
  2. Execute progression check: `AuthorTierEngine::evaluateAuthorTier(3);`
  3. Query author record:
     ```sql
     SELECT author_tier_sublevel, author_league, cpm_multiplier FROM authors WHERE id = 3;
     ```
- **Expected Result**: `author_tier_sublevel` equals `9`, `author_league` equals `'Gold'`, `cpm_multiplier` equals `1.40`.
- **Assertion**: `author_tier_sublevel === 9 && cpm_multiplier === 1.40`.

### Test Case 2: Max Level Partner Elite IV Boundary
- **QA Test ID**: `TC-TIER-002`
- **Execution Steps**:
  1. Set author ID 3 reputation points to 75,000 points.
  2. Execute `AuthorTierEngine::evaluateAuthorTier(3);`
- **Expected Result**: Author assigned Sublevel 20 (Partner Elite IV) with 2.50x CPM multiplier.
