<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EIMS | Global Logistics Synchronized</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon-logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --brand-main: #10b981;
            --brand-dark: #064e3b;
            --bg-deep: #020617;
            --card-glass: rgba(15, 23, 42, 0.6);
            --border-glass: rgba(255, 255, 255, 0.08);
        }

        * { font-family: 'Outfit', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: var(--bg-deep); color: #f8fafc; overflow-x: hidden; line-height: 1.6; }

        .bg-glow {
            position: fixed; top: -10%; left: -10%; width: 50%; height: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
            z-index: -1; pointer-events: none; animation: drift 20s infinite alternate;
        }
        @keyframes drift { from { transform: translate(0,0); } to { transform: translate(40vw, 20vh); } }

        nav {
            backdrop-filter: blur(16px); background: rgba(2, 6, 23, 0.8);
            border-bottom: 1px solid var(--border-glass); position: fixed; top: 0; width: 100%; z-index: 100;
            padding: 1.25rem 2rem; display: flex; justify-content: space-between; align-items: center;
        }

        .logo { font-size: 1.5rem; font-weight: 900; letter-spacing: -1px; display: flex; align-items: center; }
        .logo img { height: 2rem; margin-right: 0.75rem; filter: drop-shadow(0 0 10px rgba(16, 185, 129, 0.3)); }

        .nav-links { display: flex; gap: 2.5rem; }
        .nav-links a { color: #94a3b8; text-decoration: none; font-size: 0.9rem; font-weight: 700; transition: color 0.3s; }
        .nav-links a:hover { color: #fff; }

        .btn { padding: 0.85rem 1.75rem; border-radius: 0.75rem; font-weight: 800; cursor: pointer; transition: all 0.3s; border: none; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-outline { border: 1px solid var(--border-glass); background: transparent; color: #fff; }
        .btn-outline:hover { background: rgba(255,255,255,0.05); transform: translateY(-2px); }
        .btn-brand { background: var(--brand-main); color: #fff; box-shadow: 0 15px 30px -5px rgba(16, 185, 129, 0.3); }
        .btn-brand:hover { background: #059669; transform: translateY(-2px) scale(1.02); }

        .section-tag { color: var(--brand-main); font-size: 0.75rem; font-weight: 900; text-transform: uppercase; letter-spacing: 4px; display: block; margin-bottom: 1rem; }

        header.hero { padding: 12rem 2rem 8rem; max-width: 1200px; margin: 0 auto; display: flex; align-items: center; gap: 4rem; }
        .hero-text h1 { font-size: 5rem; font-weight: 900; line-height: 0.95; margin-bottom: 2rem; letter-spacing: -3px; background: linear-gradient(135deg, #fff 0%, #94a3b8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-text p { font-size: 1.35rem; color: #64748b; margin-bottom: 3rem; max-width: 500px; font-weight: 400; }

        .glass-mockup { background: var(--card-glass); backdrop-filter: blur(32px); border: 1px solid var(--border-glass); border-radius: 3rem; padding: 3rem; box-shadow: 0 50px 100px -20px rgba(0,0,0,0.7); position: relative; }

        .features { max-width: 1200px; margin: 5rem auto; padding: 0 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; }
        .feature-card { background: rgba(15,23,42,0.3); border: 1px solid var(--border-glass); padding: 3rem; border-radius: 2.5rem; transition: all 0.4s; }
        .feature-card:hover { border-color: var(--brand-main); transform: translateY(-10px); background: rgba(16, 185, 129, 0.02); }
        .feature-card h4 { font-size: 1.25rem; font-weight: 800; margin: 1.5rem 0 1rem; }
        .feature-card p { color: #64748b; font-size: 0.95rem; }

        .stats { padding: 10rem 2rem; max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; }
        .stat-item h3 { font-size: 4rem; font-weight: 900; color: #fff; margin-bottom: 0.5rem; letter-spacing: -2px; }
        .stat-item p { color: #475569; font-weight: 800; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px; }

        .testimonials { padding: 10rem 2rem; background: rgba(16, 185, 129, 0.02); }
        .testimonial-grid { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; }
        .testimonial-card { padding: 4rem; background: var(--bg-deep); border: 1px solid var(--border-glass); border-radius: 3rem; }
        .testimonial-card p { font-size: 1.25rem; color: #94a3b8; font-style: italic; margin-bottom: 2.5rem; }

        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.9); backdrop-filter: blur(12px); z-index: 200; display: flex; justify-content: center; align-items: center; }
        .modal-card { background: #0f172a; border: 1px solid var(--border-glass); width: 100%; max-width: 550px; padding: 4rem; border-radius: 3rem; }

        .input-group { margin-bottom: 1.5rem; }
        .input-group label { display: block; font-size: 0.7rem; font-weight: 900; text-transform: uppercase; color: #475569; letter-spacing: 2px; margin-bottom: 0.75rem; }
        .input-group input, .input-group textarea { width: 100%; padding: 1.25rem; border-radius: 1rem; background: #1e293b; border: 1px solid #334155; color: #fff; outline: none; transition: 0.3s; }
        .input-group input:focus { border-color: var(--brand-main); box-shadow: 0 0 20px rgba(16, 185, 129, 0.1); }

        footer { border-top: 1px solid var(--border-glass); padding: 5rem 2rem; text-align: center; color: #334155; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 3px; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="{ modalOpen: false, loading: false }">
    <div class="bg-glow"></div>

    <nav>
        <div class="logo">
            <img src="{{ asset('favicon-logo.png') }}" alt="logo">
            <span>EIMS</span>
        </div>
        <div class="nav-links">
            <a href="#features">Solutions</a>
            <a href="#about">Infrastructure</a>
            <a href="#network">Network</a>
        </div>
        <div class="actions">
            <a href="/login" class="btn btn-outline" style="margin-right: 1.5rem;">Member Portal</a>
            <button @click="modalOpen = true" class="btn btn-brand">Request Access</button>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-text">
            <span class="section-tag">Tier 1 Enterprise</span>
            <h1>Global Logistics.<br>Synchronized.</h1>
            <p>Unified infrastructure for the world's most complex supply chains. High-fidelity observability, multi-tenant security, and zero-latency orchestration.</p>
            <div style="display: flex; gap: 1.5rem;">
                <button @click="modalOpen = true" class="btn btn-brand" style="padding: 1.25rem 2.5rem; font-size: 1rem;">Get Started Now &rarr;</button>
                <a href="#features" class="btn btn-outline" style="padding: 1.25rem 2.5rem; font-size: 1rem;">Core Solutions</a>
            </div>
        </div>
        <div style="flex: 1;">
            <div class="glass-mockup">
                <div style="display: flex; gap: 0.75rem; margin-bottom: 2.5rem;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444;"></div>
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: #f59e0b;"></div>
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: #10b981;"></div>
                </div>
                <div style="height: 12px; width: 40%; background: #334155; border-radius: 10px; margin-bottom: 1.5rem;"></div>
                <div style="height: 12px; width: 85%; background: #1e293b; border-radius: 10px; margin-bottom: 0.75rem;"></div>
                <div style="height: 12px; width: 65%; background: #1e293b; border-radius: 10px; margin-bottom: 3rem;"></div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
                    <div style="height: 80px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 1.5rem;"></div>
                    <div style="height: 80px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 1.5rem;"></div>
                    <div style="height: 80px; background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2); border-radius: 1.5rem;"></div>
                </div>
            </div>
        </div>
    </header>

    <section id="features" class="features">
        <div class="feature-card">
            <span style="font-size: 2rem;">🔍</span>
            <h4>Real-time Visibility</h4>
            <p>End-to-end tracking across all supply chain stages with instant Redis-driven synchronization.</p>
        </div>
        <div class="feature-card">
            <span style="font-size: 2rem;">🧠</span>
            <h4>Predictive Analytics</h4>
            <p>Heuristic insights to optimize stock levels and forecast demand with institutional precision.</p>
        </div>
        <div class="feature-card">
            <span style="font-size: 2rem;">⚙️</span>
            <h4>Automated Flows</h4>
            <p>Streamline operations with high-frequency event triggers and atomic process orchestration.</p>
        </div>
        <div class="feature-card">
            <span style="font-size: 2rem;">🛡️</span>
            <h4>Global Compliance</h4>
            <p>Zero-trust security architecture ensuring regulatory adherence across all international borders.</p>
        </div>
    </section>

    <section class="stats">
        <div class="stat-item"><h3>99.9%</h3><p>Infrastructure Uptime</p></div>
        <div class="stat-item"><h3>50ms</h3><p>Global Latency</p></div>
        <div class="stat-item"><h3>150+</h3><p>Enterprise Nodes</p></div>
        <div class="stat-item"><h3>1.2M</h3><p>Daily Transactions</p></div>
    </section>

    <section id="about" style="padding: 12rem 2rem; max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1.2fr; gap: 8rem; align-items: center;">
        <div>
            <span class="section-tag">Engineering</span>
            <h2 style="font-size: 3.5rem; font-weight: 900; line-height: 1; margin-bottom: 2.5rem; letter-spacing: -2px;">Built for the<br><span style="color: var(--brand-main);">Modern Enterprise.</span></h2>
            <p style="color: #64748b; font-size: 1.15rem; margin-bottom: 3rem;">EIMS provides a unified infrastructure for global organizations. Our platform is engineered for data integrity and multi-tenant isolation.</p>
            <div style="display: grid; gap: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;"><span style="color: var(--brand-main); font-weight: 900;">✔</span> <span style="font-weight: 700;">Institutional isolation layer</span></div>
                <div style="display: flex; align-items: center; gap: 1rem;"><span style="color: var(--brand-main); font-weight: 900;">✔</span> <span style="font-weight: 700;">Real-time health telemetry</span></div>
                <div style="display: flex; align-items: center; gap: 1rem;"><span style="color: var(--brand-main); font-weight: 900;">✔</span> <span style="font-weight: 700;">Zero-trust auth matrix</span></div>
            </div>
        </div>
        <div style="background: rgba(16, 185, 129, 0.03); border: 1px solid rgba(16, 185, 129, 0.1); border-radius: 4rem; padding: 5rem;">
             <div style="font-size: 0.8rem; font-weight: 900; color: var(--brand-main); margin-bottom: 1.5rem; letter-spacing: 2px;">CORE STACK</div>
             <div style="display: grid; gap: 2rem;">
                 <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1rem;">
                     <span style="font-weight: 700;">Database Layer</span>
                     <span style="color: #64748b;">Isolated MySQL Cluster</span>
                 </div>
                 <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1rem;">
                     <span style="font-weight: 700;">Caching Fabric</span>
                     <span style="color: #64748b;">Global Redis Network</span>
                 </div>
                 <div style="display: flex; justify-content: space-between;">
                     <span style="font-weight: 700;">Auth Engine</span>
                     <span style="color: #64748b;">Z-Trust / RBAC</span>
                 </div>
             </div>
        </div>
    </section>

    <section id="network" style="padding: 10rem 2rem; text-align: center;">
        <span class="section-tag">Global Mesh</span>
        <h2 style="font-size: 3.5rem; font-weight: 900; margin-bottom: 5rem; letter-spacing: -2px;">Orchestrate from any Node.</h2>
        <div style="max-width: 800px; margin: 0 auto; opacity: 0.2;">
            <svg viewBox="0 0 800 400" fill="none" stroke="var(--brand-main)" stroke-width="1">
                <path d="M150 100 Q 400 50 650 100 T 750 300 Q 400 350 50 300 T 150 100" />
                <path d="M150 100 L 400 200 L 650 100 L 750 300 L 50 300 Z" />
            </svg>
        </div>
    </section>

    <section id="cta" style="background: linear-gradient(to bottom, transparent, rgba(16, 185, 129, 0.05)); padding: 12rem 2rem; text-align: center;">
        <h2 style="font-size: 4rem; font-weight: 900; margin-bottom: 2rem; letter-spacing: -3px;">Sync your assets today.</h2>
        <div style="display: flex; justify-content: center; gap: 2rem;">
            <a href="/login" class="btn btn-outline" style="padding: 1.5rem 4rem; font-size: 1.1rem;">Member Portal</a>
            <button @click="modalOpen = true" class="btn btn-brand" style="padding: 1.5rem 4rem; font-size: 1.1rem;">Request Access</button>
        </div>
    </section>

    <footer>&copy; 2026 EIMS Enterprise Inventory. All Rights Reserved.</footer>

    <div x-show="modalOpen" class="modal-overlay" x-cloak x-transition>
        <div class="modal-card" @click.away="modalOpen = false">
            <h3 style="font-size: 2rem; font-weight: 900; margin-bottom: 2.5rem; letter-spacing: -1px;">Request Onboarding</h3>
            <form @submit.prevent="
                loading = true;
                const formData = new FormData($el);
                fetch('/request-access', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    loading = false;
                    if(data.success) {
                        Swal.fire({ icon: 'success', title: 'Request Sent', text: data.message, background: '#0f172a', color: '#fff', confirmButtonColor: '#10b981' });
                        modalOpen = false; $el.reset();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#0f172a', color: '#fff' });
                    }
                })">
                <div class="input-group">
                    <label>Corporate Email</label>
                    <input type="email" name="email" required placeholder="name@company.com">
                </div>
                <div class="input-group">
                    <label>Organization Name</label>
                    <input type="text" name="organization_name" required placeholder="Global Logistics Inc.">
                </div>
                <div class="input-group">
                    <label>Brief Message (Optional)</label>
                    <textarea name="message" rows="3" placeholder="Operational requirements..."></textarea>
                </div>
                <button type="submit" class="btn btn-brand" style="width: 100%; padding: 1.5rem;" :disabled="loading">
                    <span x-show="!loading">Submit Request</span>
                    <span x-show="loading">Processing...</span>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
