-- ============================================================================
-- TechScout AI Image Prompts & Categories Upgrade
-- Dedicated Image Generation Engines: Midjourney, DALL-E 3, Flux.1, Stable Diffusion
-- Safe & Idempotent
-- ============================================================================

-- Step 1: Insert Image Prompt Categories if not present
INSERT IGNORE INTO `categories` (`id`, `parent_id`, `topic_id`, `name`, `slug`, `description`, `icon_name`, `color_hex`, `applicable_to`, `sort_order`)
VALUES 
(30, NULL, 1, 'AI Image Generation', 'ai-image-prompts', 'Curated prompts for Midjourney, DALL-E 3, Flux.1, and Stable Diffusion with parameters and negative prompts.', 'image', '#ec4899', 'prompt', 10),
(31, 30, 1, 'Midjourney v6', 'midjourney', 'Hyper-realistic, cinematic, and stylized prompts with aspect ratio and stylistic parameters.', 'camera', '#8b5cf6', 'prompt', 11),
(32, 30, 1, 'DALL-E 3', 'dall-e', 'High-coherence typography, vectors, illustrations, and 3D render blueprints for OpenAI DALL-E 3.', 'sparkles', '#10b981', 'prompt', 12),
(33, 30, 1, 'Flux.1 Dev/Schnell', 'flux', 'Next-generation open weights diffusion prompts with state-of-the-art anatomy and typography fidelity.', 'zap', '#f59e0b', 'prompt', 13),
(34, 30, 1, 'Stable Diffusion XL', 'stable-diffusion', 'SDXL and checkpoint blueprints with negative prompts, LoRA weights, and CFG scales.', 'cpu', '#06b6d4', 'prompt', 14);

-- Step 2: Insert Real Curated Image Prompts into `contents` and `prompts`

-- 1. Midjourney: Cyberpunk Tokyo Rain
INSERT IGNORE INTO `contents` (`id`, `uuid`, `content_type`, `author_id`, `category_id`, `title`, `slug`, `summary`, `body_html`, `featured_image_url`, `status`, `visibility`, `is_featured`, `is_trending`, `view_count`, `reaction_count`, `published_at`, `created_at`, `updated_at`)
VALUES (
  41, UUID(), 'prompt', 1, 31, 
  'Cinematic Cyberpunk Rain Alleyway & Neon Reflections', 
  'midjourney-cyberpunk-rain-alleyway',
  'Ultra-detailed 8k photograph of a futuristic neon-drenched Tokyo alleyway in heavy rainfall with anamorphic bokeh and cinematic lighting.',
  '<p>Master prompt blueprint engineered for Midjourney v6 producing cinematic atmospheric depth, realistic asphalt water reflections, and natural anamorphic lens bokeh.</p>',
  'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=1200&auto=format&fit=crop&q=80',
  'published', 'public', 1, 1, 842, 126, NOW(), NOW(), NOW()
);

INSERT IGNORE INTO `prompts` (`content_id`, `prompt_template`, `template_variables`, `target_models`, `copy_count`, `sample_output`)
VALUES (
  41,
  'Cinematic 35mm photograph of a narrow neon-lit street in Neo-Tokyo during a heavy downpour, hyper-realistic wet asphalt reflections, holographic Japanese billboards, lone detective wearing an illuminated cyber trench coat, atmospheric steam, volumetric lighting, shot on ARRI Alexa Mini, Kodak Vision3 500T, anamorphic bokeh, photorealistic 8k --ar 16:9 --v 6.0 --style raw --c 5 --s 250',
  '["LOCATION", "LIGHTING", "PROTAGONIST", "ASPECT_RATIO"]',
  '["Midjourney v6", "Midjourney"]',
  342,
  'Hyper-realistic rain drenched cinematic frame with intense volumetric neon highlights and natural motion blur.'
);

-- 2. DALL-E 3: Isometric Glassmorphism SaaS Architecture
INSERT IGNORE INTO `contents` (`id`, `uuid`, `content_type`, `author_id`, `category_id`, `title`, `slug`, `summary`, `body_html`, `featured_image_url`, `status`, `visibility`, `is_featured`, `is_trending`, `view_count`, `reaction_count`, `published_at`, `created_at`, `updated_at`)
VALUES (
  42, UUID(), 'prompt', 1, 32,
  'Isometric Glassmorphic Cloud Architecture & Microservices Matrix',
  'dalle3-isometric-glassmorphic-cloud-architecture',
  '3D isometric clean tech visualization of modular microservices floating on frosted glass layers with glowing data buses and pastel gradient lighting.',
  '<p>Engineered for technical presentation decks, landing pages, and documentation headers requiring crisp clean 3D isometric systems visualization.</p>',
  'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&auto=format&fit=crop&q=80',
  'published', 'public', 1, 1, 615, 98, NOW(), NOW(), NOW()
);

INSERT IGNORE INTO `prompts` (`content_id`, `prompt_template`, `template_variables`, `target_models`, `copy_count`, `sample_output`)
VALUES (
  42,
  'Detailed 3D isometric render of a futuristic cloud computing system architecture, glowing frosted acrylic and transparent glass layers, floating modular microservice cubes with illuminated data nodes, glowing fiber-optic streams connecting databases, soft studio ambient occlusion, elegant purple and cyan gradient illumination, clean white minimalist background, 8k octane render style.',
  '["COMPONENTS", "LAYER_STYLE", "COLOR_PALETTE"]',
  '["DALL-E 3", "OpenAI ChatGPT"]',
  219,
  'Clean 3D floating isometric diagram with transparent refractive glass cards and vivid data pipelines.'
);

-- 3. Flux.1: Hyper-Detailed Studio Portrait of AI Robot
INSERT IGNORE INTO `contents` (`id`, `uuid`, `content_type`, `author_id`, `category_id`, `title`, `slug`, `summary`, `body_html`, `featured_image_url`, `status`, `visibility`, `is_featured`, `is_trending`, `view_count`, `reaction_count`, `published_at`, `created_at`, `updated_at`)
VALUES (
  43, UUID(), 'prompt', 1, 33,
  'Flux.1 Hyper-Realistic Biomechanical Android Portrait',
  'flux-biomechanical-android-portrait',
  'Studio portrait photography of an elegant humanoid android with visible ceramic chassis seams, optical iris sensors, and natural studio softbox rim lighting.',
  '<p>Utilizes Flux.1 state-of-the-art text and anatomy fidelity to generate ultra-sharp studio photography of human-machine coexistence.</p>',
  'https://images.unsplash.com/photo-1618172193763-c511deb635ca?w=1200&auto=format&fit=crop&q=80',
  'published', 'public', 1, 0, 520, 74, NOW(), NOW(), NOW()
);

INSERT IGNORE INTO `prompts` (`content_id`, `prompt_template`, `template_variables`, `target_models`, `copy_count`, `sample_output`)
VALUES (
  43,
  'Editorial medium closeup studio portrait of an elegant humanoid synth with matte white ceramic facial plating and subtle brushed copper internal wiring along the jawline, hyper-detailed mechanical hazel glass eyes reflecting softbox light, natural skin micro-texture, depth of field, Hasselblad H6D-100c, 100mm f/2.2 lens, neutral dark gray gradient background, ultra realistic 8k resolution.',
  '["SUBJECT", "MATERIALS", "LIGHTING", "CAMERA"]',
  '["Flux.1 Dev", "Flux.1 Schnell"]',
  188,
  'Pristine studio portrait with tangible material textures, subtle ceramic reflections, and lifelike depth of field.'
);

-- 4. Stable Diffusion XL: Futuristic Data Center Server Vault
INSERT IGNORE INTO `contents` (`id`, `uuid`, `content_type`, `author_id`, `category_id`, `title`, `slug`, `summary`, `body_html`, `featured_image_url`, `status`, `visibility`, `is_featured`, `is_trending`, `view_count`, `reaction_count`, `published_at`, `created_at`, `updated_at`)
VALUES (
  44, UUID(), 'prompt', 1, 34,
  'SDXL Deep Subsea Quantum Data Center & Fiber Optic Matrix',
  'sdxl-subsea-quantum-datacenter',
  'High-contrast wide architectural photography of a submerged high-performance computing data hall with submerged cooling tanks and neon blue status LEDs.',
  '<p>Optimized for SDXL 1.0 base with negative prompt conditioning to prevent blur, artifacting, and unnatural symmetry.</p>',
  'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1200&auto=format&fit=crop&q=80',
  'published', 'public', 0, 1, 478, 62, NOW(), NOW(), NOW()
);

INSERT IGNORE INTO `prompts` (`content_id`, `prompt_template`, `template_variables`, `target_models`, `copy_count`, `sample_output`)
VALUES (
  44,
  'Wide angle architectural interior photograph of a state-of-the-art underwater quantum supercomputer server room, immersion cooling tanks bubbling with liquid fluorochemical, racks of glowing blade servers with pulsing azure and amber status LEDs, reinforced titanium reinforced curved ceiling with dark ocean water visible through thick quartz glass portholes, moody volumetric haze, architectural digest photography, shot on Sony A7R V, 14mm f/2.8 lens, sharp focus throughout. Negative prompt: cartoon, illustration, lowres, oversaturated, blurry, distorted perspective, extra structures, watermark, text.',
  '["INFRASTRUCTURE", "COOLING_TECH", "COLOR_THEME", "NEGATIVE_PROMPT"]',
  '["Stable Diffusion XL", "SDXL"]',
  156,
  'Atmospheric wide architectural interior showing liquid-cooled server racks and dramatic high-contrast lighting.'
);
