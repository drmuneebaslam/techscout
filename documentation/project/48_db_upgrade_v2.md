# 48. Database Upgrade v2 Script (`database_upgrade_v2.sql`)

## File Location
[`database/database_upgrade_v2.sql`](file:///c:/xampp/htdocs/techscout.n/database/database_upgrade_v2.sql)

## Schema Updates
- Adds `author_tier_sublevel` and `cpm_multiplier` columns to `authors`.
- Creates `co_authors` relationship table for multi-author articles.
- Adds `revenue_split_percent` columns to content earnings tables.
