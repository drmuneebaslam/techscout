<?php
namespace App\Models;

use App\Core\Database;
use App\Models\AuditLog;

class PersonaFollowerEngine {

    /**
     * Curated roster of 20 realistic technical reader & developer personas
     */
    public static function getPersonaCatalog(): array {
        return [
            [
                'username' => 'sarah_ml',
                'display_name' => 'Sarah Chen',
                'headline' => 'Staff ML Engineer & LLM Fine-Tuning',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Building production inference pipelines and exploring self-hosted model quantization.'
            ],
            [
                'username' => 'marcus_v',
                'display_name' => 'Marcus Vance',
                'headline' => 'Distributed Systems & Cloud Architect',
                'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
                'bio' => 'High-concurrency microservices, consensus protocols, and resilient infrastructure.'
            ],
            [
                'username' => 'elena_ai',
                'display_name' => 'Elena Rostova',
                'headline' => 'AI Safety & Evaluation Researcher',
                'avatar_url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Investigating red-teaming techniques and guardrails for autonomous LLM agents.'
            ],
            [
                'username' => 'kai_tanaka',
                'display_name' => 'Kai Tanaka',
                'headline' => 'High-Performance Rust & WebAssembly Dev',
                'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Systems programming, memory-safe runtimes, and low-latency network engines.'
            ],
            [
                'username' => 'aria_dev',
                'display_name' => 'Aria Montgomery',
                'headline' => 'Prompt Engineering & Agentic Workflows',
                'avatar_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Designing multi-agent orchestration frameworks with structured tool-calling.'
            ],
            [
                'username' => 'davidkim_tech',
                'display_name' => 'David Kim',
                'headline' => 'Fullstack AI Application Builder',
                'avatar_url' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Connecting vector embeddings to interactive web interfaces.'
            ],
            [
                'username' => 'zkhan_dev',
                'display_name' => 'Zubair Khan',
                'headline' => 'Zero-Trust Infrastructure & Cloud Security',
                'avatar_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Hardening Kubernetes clusters, API gateways, and IAM pipelines.'
            ],
            [
                'username' => 'amara_cloud',
                'display_name' => 'Amara Okafor',
                'headline' => 'Kubernetes Operator & Platform Engineering',
                'avatar_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Automating cloud-native deployments with custom CRDs and Helm charts.'
            ],
            [
                'username' => 'lucas_silva',
                'display_name' => 'Lucas Silva',
                'headline' => 'Vector Databases & Neural Search Specialist',
                'avatar_url' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Optimizing HNSW graphs, similarity indices, and hybrid BM25 search.'
            ],
            [
                'username' => 'chloe_ai',
                'display_name' => 'Chloe Dupont',
                'headline' => 'AI Product Strategist & Tech Scout',
                'avatar_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Analyzing emerging enterprise AI adoption trends and open-source models.'
            ],
            [
                'username' => 'dev_nathan',
                'display_name' => 'Nathan Drake',
                'headline' => 'Senior Backend & Microservices Architect',
                'avatar_url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=150&auto=format&fit=crop&q=80',
                'bio' => 'High-throughput APIs in Go, PHP Swoole, and gRPC service meshes.'
            ],
            [
                'username' => 'olivia_sec',
                'display_name' => 'Olivia Reynolds',
                'headline' => 'Application Security & Pen-Testing Lead',
                'avatar_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Securing OAuth2 flows, CSRF boundaries, and LLM prompt injection vectors.'
            ],
            [
                'username' => 'yuki_prompt',
                'display_name' => 'Yuki Sato',
                'headline' => 'Generative AI Creative & Prompt Hacker',
                'avatar_url' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Diffusion models, LoRA fine-tuning, and multimodal generation.'
            ],
            [
                'username' => 'felix_rust',
                'display_name' => 'Felix Weber',
                'headline' => 'Systems Programmer & Linux Kernel Dev',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
                'bio' => 'eBPF tracing, memory allocators, and Linux performance tuning.'
            ],
            [
                'username' => 'tariq_ops',
                'display_name' => 'Tariq Mahmood',
                'headline' => 'Site Reliability & Cloud Observability',
                'avatar_url' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Prometheus alerts, Grafana dashboards, and OpenTelemetry instrumentation.'
            ],
            [
                'username' => 'hannah_data',
                'display_name' => 'Hannah Abbott',
                'headline' => 'Data Pipeline & Real-Time Streaming Lead',
                'avatar_url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Apache Flink, Kafka event streams, and real-time feature engineering.'
            ],
            [
                'username' => 'alex_frontend',
                'display_name' => 'Alex Rivera',
                'headline' => 'Modern Web Performance & Architecture',
                'avatar_url' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Vanilla CSS mastery, zero-runtime frameworks, and WebGL visualizations.'
            ],
            [
                'username' => 'raj_systems',
                'display_name' => 'Raj Patel',
                'headline' => 'Database Optimization & Distributed Caching',
                'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
                'bio' => 'MySQL query optimization, Redis clustering, and database sharding.'
            ],
            [
                'username' => 'maya_infra',
                'display_name' => 'Maya Lin',
                'headline' => 'Event-Driven Systems & Kafka Architect',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Event sourcing, CQRS patterns, and asynchronous messaging pipelines.'
            ],
            [
                'username' => 'sven_k8s',
                'display_name' => 'Sven Lindqvist',
                'headline' => 'Cloud Native & DevOps Vanguard',
                'avatar_url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Terraform IaC, GitOps with ArgoCD, and automated deployment canary tests.'
            ]
        ];
    }

    /**
     * Ensure a persona user exists in the database with their author/member profile
     */
    public static function getOrCreatePersonaUser(array $persona): int {
        $username = $persona['username'];
        $existing = Database::fetch("SELECT id FROM users WHERE username = ?", [$username]);
        if ($existing) {
            $userId = (int)$existing['id'];
        } else {
            $email = $username . '@techscout.local';
            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
            $hash = password_hash('PersonaPass123!', PASSWORD_DEFAULT);
            Database::execute("
                INSERT INTO users (uuid, email, password_hash, username, role, status, email_verified_at, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'member', 'active', NOW(), NOW(), NOW())
            ", [$uuid, $email, $hash, $username]);
            $userId = (int)Database::lastInsertId();
        }

        // Ensure author profile exists with persona metadata
        $existingAuthor = Database::fetch("SELECT id FROM authors WHERE user_id = ?", [$userId]);
        if (!$existingAuthor) {
            Database::execute("
                INSERT INTO authors (user_id, display_name, slug, headline, bio, avatar_url, reputation_score, follower_count, total_views, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, 150, 0, 0, NOW(), NOW())
            ", [
                $userId,
                $persona['display_name'],
                $persona['username'],
                $persona['headline'],
                $persona['bio'],
                $persona['avatar_url']
            ]);
        } else {
            Database::execute("
                UPDATE authors 
                SET display_name = ?, headline = ?, avatar_url = COALESCE(NULLIF(avatar_url, ''), ?), bio = COALESCE(NULLIF(bio, ''), ?)
                WHERE user_id = ?
            ", [
                $persona['display_name'],
                $persona['headline'],
                $persona['avatar_url'],
                $persona['bio'],
                $userId
            ]);
        }

        return $userId;
    }

    /**
     * Inject realistic persona followers for an author
     */
    public static function injectPersonaFollowers(int $authorId, int $count = 10): int {
        $catalog = self::getPersonaCatalog();
        shuffle($catalog);
        $added = 0;

        foreach ($catalog as $persona) {
            if ($added >= $count) break;

            $userId = self::getOrCreatePersonaUser($persona);

            // Check if already following
            $already = Database::fetch("SELECT id FROM author_followers WHERE author_id = ? AND user_id = ?", [$authorId, $userId]);
            if (!$already) {
                $hoursAgo = rand(1, 168); // Between 1 hour and 7 days ago
                Database::execute("
                    INSERT INTO author_followers (author_id, user_id, created_at)
                    VALUES (?, ?, DATE_SUB(NOW(), INTERVAL ? HOUR))
                ", [$authorId, $userId, $hoursAgo]);
                $added++;
            }
        }

        // Sync author follower counter
        $realFollowerCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM author_followers WHERE author_id = ?", [$authorId])['c'] ?? 0);
        Database::execute("UPDATE authors SET follower_count = GREATEST(follower_count, ?) WHERE id = ?", [$realFollowerCount, $authorId]);

        AuditLog::record('author.persona_followers_injected', 'authors', $authorId, [
            'injected_count' => $added,
            'total_followers' => $realFollowerCount
        ]);

        return $added;
    }

    /**
     * Get followers roster for author (handles both real members and persona profiles)
     */
    public static function getAuthorFollowers(int $authorId, int $limit = 60): array {
        return Database::query("
            SELECT af.created_at as followed_at, u.id as user_id, u.username,
                   COALESCE(a.display_name, u.username) as display_name,
                   COALESCE(a.avatar_url, '') as avatar_url,
                   a.id as author_id, a.slug as author_slug,
                   COALESCE(a.headline, 'Verified TechScout Community Member') as headline,
                   COALESCE(a.reputation_score, 100) as reputation_score,
                   COALESCE(a.verification_badge, '') as verification_badge
            FROM author_followers af
            JOIN users u ON af.user_id = u.id
            LEFT JOIN authors a ON a.user_id = u.id
            WHERE af.author_id = ?
            ORDER BY af.created_at DESC
            LIMIT " . max(1, (int)$limit), 
            [$authorId]
        );
    }
}
