<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Campus Hub - Professional Transport Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px 40px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        
        .logo-text h1 {
            font-size: 24px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .logo-text p {
            color: #666;
            font-size: 14px;
        }
        
        nav {
            display: flex;
            gap: 30px;
        }
        
        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        nav a:hover {
            color: #667eea;
        }
        
        .hero {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 60px 40px;
            margin-bottom: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            text-align: center;
        }
        
        .hero h2 {
            font-size: 48px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero p {
            font-size: 20px;
            color: #666;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .dashboard-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .dashboard-section h2 {
            font-size: 36px;
            margin-bottom: 30px;
            text-align: center;
            color: #333;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .dashboard-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
        }
        
        .dashboard-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .dashboard-card .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-enhanced {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-complete {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .dashboard-card p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .dashboard-card .btn {
            width: 100%;
            text-align: center;
        }
        
        .tech-stack {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .tech-badge {
            background: #f5f5f5;
            color: #333;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        footer {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        footer p {
            color: #666;
        }
        
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 20px;
            }
            
            nav {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .hero h2 {
                font-size: 32px;
            }
            
            .hero p {
                font-size: 16px;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <div class="logo-icon">🚌</div>
                <div class="logo-text">
                    <h1>Smart Campus Hub</h1>
                    <p>Professional Transport Management</p>
                </div>
            </div>
            <nav>
                <a href="#features">Features</a>
                <a href="#dashboards">Dashboards</a>
                <a href="#contact">Contact</a>
            </nav>
        </header>

        <section class="hero">
            <h2>Advanced Campus Transportation System</h2>
            <p>Professional transport management with real-time tracking, modern UI/UX, and comprehensive role-based access control for educational institutions.</p>
            <div class="cta-buttons">
                <a href="#dashboards" class="btn btn-primary">View Dashboards</a>
                <a href="enhanced_dashboard.php" class="btn btn-secondary">Live Demo</a>
            </div>
        </section>

        <section id="features" class="features">
            <div class="feature-card">
                <div class="feature-icon">🎨</div>
                <h3>Modern UI/UX Design</h3>
                <p>Glassmorphism effects, dark mode themes, powerful animations, and responsive design create an impressive user experience across all devices.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🗺️</div>
                <h3>Real-Time Tracking</h3>
                <p>Interactive maps with live bus positions, ETA calculations, and GPS broadcasting for accurate transportation monitoring.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔐</div>
                <h3>Role-Based Access</h3>
                <p>Secure authentication system with different interfaces for administrators, students, and drivers with appropriate permissions.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📄</div>
                <h3>Professional PDF Generation</h3>
                <p>Automated permit generation with QR codes, system verification badges, and modern professional styling.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3>Mobile Optimized</h3>
                <p>Fully responsive design with touch gestures, mobile-friendly interfaces, and optimized performance for all devices.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Advanced Features</h3>
                <p>Toast notifications, quick verify modals, capacity monitoring, and interactive elements for enhanced functionality.</p>
            </div>
        </section>

        <section id="dashboards" class="dashboard-section">
            <h2>Professional Dashboards</h2>
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>
                        Enhanced Admin Dashboard 
                        <span class="badge badge-enhanced">ENHANCED</span>
                    </h3>
                    <p>Flagship dashboard with glassmorphism UI, particle animations, 3D transforms, and advanced interactive features.</p>
                    <a href="enhanced_dashboard.php" class="btn btn-primary">Access Dashboard</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>
                        Admin Dashboard
                        <span class="badge badge-complete">COMPLETE</span>
                    </h3>
                    <p>Professional admin interface with RBAC permissions, permit management, and system oversight capabilities.</p>
                    <a href="admin_dashboard_rbac.php" class="btn btn-secondary">Access Dashboard</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>
                        User Dashboard
                        <span class="badge badge-complete">COMPLETE</span>
                    </h3>
                    <p>Student portal for permit management, personal transportation services, and QR code verification.</p>
                    <a href="permits.php" class="btn btn-secondary">Access Dashboard</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>
                        Driver Dashboard
                        <span class="badge badge-complete">COMPLETE</span>
                    </h3>
                    <p>Professional driver interface for trip management, GPS broadcasting, and passenger verification.</p>
                    <a href="driver_portal_rbac.php" class="btn btn-secondary">Access Dashboard</a>
                </div>
            </div>
        </section>

        <section class="dashboard-section">
            <h2>System Components</h2>
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>📄 PDF Generator</h3>
                    <p>Professional PDF permit generation with modern styling, system verification badges, and QR code integration.</p>
                    <a href="generate_permit.php" class="btn btn-primary">Generate PDF</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>🗺️ Live Tracking</h3>
                    <p>Interactive real-time map with bus positions, route visualization, and GPS tracking capabilities.</p>
                    <a href="live_tracking.php" class="btn btn-primary">Live Map</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>🔐 Authentication</h3>
                    <p>Secure login system with role-based redirection and comprehensive access control management.</p>
                    <a href="login.php" class="btn btn-primary">Login System</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>🔍 Verification</h3>
                    <p>QR code verification system for permit authentication and security validation.</p>
                    <a href="verify.php" class="btn btn-primary">Verification</a>
                </div>
            </div>
        </section>

        <section class="dashboard-section">
            <h2>Technology Stack</h2>
            <div style="text-align: center; margin-bottom: 30px;">
                <p>Built with modern technologies for optimal performance and user experience.</p>
            </div>
            <div class="tech-stack">
                <span class="tech-badge">PHP</span>
                <span class="tech-badge">MySQL</span>
                <span class="tech-badge">JavaScript</span>
                <span class="tech-badge">HTML5</span>
                <span class="tech-badge">CSS3</span>
                <span class="tech-badge">Tailwind CSS</span>
                <span class="tech-badge">Leaflet.js</span>
                <span class="tech-badge">Font Awesome</span>
            </div>
        </section>

        <section id="contact" class="dashboard-section">
            <h2>Contact Us</h2>
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>📧 Send Feedback</h3>
                    <p>We value your feedback and suggestions for improving Smart Campus Hub.</p>
                    <form id="feedbackForm" style="margin-top: 20px;">
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Name</label>
                            <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Email</label>
                            <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Feedback</label>
                            <textarea name="feedback" required rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Feedback</button>
                    </form>
                    <div id="feedbackMessage" style="margin-top: 15px; padding: 10px; border-radius: 5px; display: none;"></div>
                </div>
                
                <div class="dashboard-card">
                    <h3>📞 Contact Information</h3>
                    <p>Get in touch with us for support, partnerships, or general inquiries.</p>
                    <div style="margin-top: 20px;">
                        <p><strong>📧 Email:</strong> support@smartcampushub.com</p>
                        <p><strong>📱 Phone:</strong> +1 (555) 123-4567</p>
                        <p><strong>📍 Address:</strong> 123 Campus Drive, Education City, EC 12345</p>
                        <p><strong>🕐 Hours:</strong> Mon-Fri 8AM-6PM, Sat-Sun 9AM-4PM</p>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <h3>🚀 Quick Links</h3>
                    <p>Access important system components and documentation.</p>
                    <div style="margin-top: 20px;">
                        <a href="enhanced_dashboard.php" class="btn btn-secondary" style="display: block; margin-bottom: 10px;">Enhanced Dashboard</a>
                        <a href="admin_dashboard_rbac.php" class="btn btn-secondary" style="display: block; margin-bottom: 10px;">Admin Dashboard</a>
                        <a href="permits.php" class="btn btn-secondary" style="display: block; margin-bottom: 10px;">User Dashboard</a>
                        <a href="driver_portal_rbac.php" class="btn btn-secondary" style="display: block;">Driver Dashboard</a>
                    </div>
                </div>
            </div>
        </section>

        <footer>
            <p>&copy; 2026 Smart Campus Hub. Professional Transport Management System.</p>
        </footer>
    </div>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add hover effects to cards
        document.querySelectorAll('.feature-card, .dashboard-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Handle feedback form submission
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const feedbackMessage = document.getElementById('feedbackMessage');
            
            // Show loading message
            feedbackMessage.style.display = 'block';
            feedbackMessage.style.background = '#e3f2fd';
            feedbackMessage.style.color = '#1976d2';
            feedbackMessage.innerHTML = '📤 Submitting your feedback...';
            
            // Simulate form submission (in real app, this would send to server)
            setTimeout(() => {
                feedbackMessage.style.background = '#e8f5e8';
                feedbackMessage.style.color = '#2e7d32';
                feedbackMessage.innerHTML = '✅ Thank you! Your feedback has been submitted successfully.';
                
                // Reset form
                this.reset();
                
                // Hide success message after 5 seconds
                setTimeout(() => {
                    feedbackMessage.style.display = 'none';
                }, 5000);
            }, 1500);
        });
    </script>
</body>
</html>
