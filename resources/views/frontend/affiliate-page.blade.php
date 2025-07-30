<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Become an Affiliate | YourBrand</title>
  <style>
    :root {
      --primary: #4F46E5;
      --primary-dark: #4338CA;
      --secondary: #10B981;
      --light: #F9FAFB;
      --dark: #1F2937;
      --gray: #6B7280;
      --border: #E5E7EB;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: var(--light);
      color: var(--dark);
      line-height: 1.6;
    }

    header {
      background-color: white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: bold;
      color: var(--primary);
    }

    nav ul {
      display: flex;
      list-style: none;
    }

    nav ul li {
      margin-left: 2rem;
    }

    nav ul li a {
      text-decoration: none;
      color: var(--dark);
      font-weight: 500;
      transition: color 0.3s;
    }

    nav ul li a:hover {
      color: var(--primary);
    }

    .hero {
      background-color: var(--primary);
      color: white;
      padding: 5rem 2rem;
      text-align: center;
    }

    .hero h1 {
      font-size: 3rem;
      margin-bottom: 1rem;
    }

    .hero p {
      font-size: 1.25rem;
      max-width: 800px;
      margin: 0 auto 2rem;
    }

    .cta-button {
      display: inline-block;
      background-color: white;
      color: var(--primary);
      padding: 0.75rem 2rem;
      border-radius: 4px;
      font-weight: bold;
      text-decoration: none;
      transition: all 0.3s;
    }

    .cta-button:hover {
      background-color: var(--light);
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .benefits {
      padding: 5rem 2rem;
      max-width: 1200px;
      margin: 0 auto;
      text-align: center;
    }

    .benefits h2 {
      font-size: 2.5rem;
      margin-bottom: 3rem;
      color: var(--dark);
    }

    .benefits-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
    }

    .benefit-card {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      transition: transform 0.3s;
    }

    .benefit-card:hover {
      transform: translateY(-5px);
    }

    .benefit-icon {
      font-size: 2.5rem;
      color: var(--primary);
      margin-bottom: 1rem;
    }

    .how-it-works {
      background-color: white;
      padding: 5rem 2rem;
    }

    .how-it-works h2 {
      font-size: 2.5rem;
      text-align: center;
      margin-bottom: 3rem;
    }

    .steps {
      max-width: 900px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    .step {
      display: flex;
      align-items: flex-start;
      gap: 2rem;
    }

    .step-number {
      background-color: var(--primary);
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      flex-shrink: 0;
    }

    .step-content h3 {
      margin-bottom: 0.5rem;
    }

    .commission {
      padding: 5rem 2rem;
      background-color: var(--light);
      text-align: center;
    }

    .commission h2 {
      font-size: 2.5rem;
      margin-bottom: 3rem;
    }

    .commission-rates {
      display: flex;
      justify-content: center;
      gap: 2rem;
      flex-wrap: wrap;
    }

    .rate-card {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      width: 280px;
      text-align: center;
    }

    .rate-value {
      font-size: 3rem;
      font-weight: bold;
      color: var(--primary);
      margin: 1rem 0;
    }

    .testimonials {
      padding: 5rem 2rem;
      background-color: white;
    }

    .testimonials h2 {
      font-size: 2.5rem;
      text-align: center;
      margin-bottom: 3rem;
    }

    .testimonial-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .testimonial-card {
      background-color: var(--light);
      padding: 2rem;
      border-radius: 8px;
    }

    .testimonial-text {
      font-style: italic;
      margin-bottom: 1rem;
    }

    .testimonial-author {
      font-weight: bold;
    }

    .faq {
      padding: 5rem 2rem;
      max-width: 900px;
      margin: 0 auto;
    }

    .faq h2 {
      font-size: 2.5rem;
      text-align: center;
      margin-bottom: 3rem;
    }

    .faq-item {
      border-bottom: 1px solid var(--border);
      padding: 1.5rem 0;
    }

    .faq-question {
      font-weight: bold;
      margin-bottom: 1rem;
      font-size: 1.1rem;
    }

    .join-section {
      background-color: var(--primary);
      color: white;
      padding: 5rem 2rem;
      text-align: center;
    }

    .join-section h2 {
      font-size: 2.5rem;
      margin-bottom: 1.5rem;
    }

    .join-section p {
      font-size: 1.25rem;
      max-width: 700px;
      margin: 0 auto 2rem;
    }

    form {
      max-width: 500px;
      margin: 0 auto;
      background: white;
      padding: 2rem;
      border-radius: 8px;
      text-align: left;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      color: var(--dark);
      font-weight: 500;
    }

    input, textarea {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--border);
      border-radius: 4px;
      font-size: 1rem;
    }

    .submit-button {
      background-color: var(--primary);
      color: white;
      border: none;
      padding: 0.75rem 2rem;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
      width: 100%;
      font-size: 1rem;
      transition: background-color 0.3s;
    }

    .submit-button:hover {
      background-color: var(--primary-dark);
    }

    footer {
      background-color: var(--dark);
      color: white;
      padding: 3rem 2rem;
      text-align: center;
    }

    .footer-links {
      display: flex;
      justify-content: center;
      gap: 2rem;
      margin-bottom: 2rem;
    }

    .footer-links a {
      color: white;
      text-decoration: none;
    }

    .footer-links a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2rem;
      }

      nav ul {
        display: none;
      }

      .step {
        flex-direction: column;
      }

      .commission-rates {
        flex-direction: column;
        align-items: center;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">Pahar Theke</div>
    <nav>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Products</a></li>
        <li><a href="#">About</a></li>
        <li><a href="#">Contact</a></li>
        <li><a href="#" style="color: var(--primary); font-weight: bold;">Affiliate Program</a></li>
      </ul>
    </nav>
  </header>

  <section class="hero">
    <h1>Earn money by partnering with one of the world's finest mountain products</h1>
    <p>Join our affiliate program today and start earning generous commissions by sharing our premium products with your audience.</p>
    <a href="https://pahartheke.com/affiliate/register" class="cta-button">Become an Affiliate</a>
  </section>

  <section class="benefits">
    <h2>Why Become Our Affiliate?</h2>
    <div class="benefits-grid">
      <div class="benefit-card">
        <div class="benefit-icon">💰</div>
        <h3>High Commissions</h3>
        <p>Earn 20 to 100 BDT per unit depend on products commission on every sale you generate through your unique affiliate links.</p>
      </div>
      <div class="benefit-card">
        <div class="benefit-icon">⚡</div>
        <h3>Instant Approval</h3>
        <p>Get started immediately with our streamlined application process and user-friendly dashboard.</p>
      </div>
      <div class="benefit-card">
        <div class="benefit-icon">📊</div>
        <h3>Real-time Analytics</h3>
        <p>Track your performance with comprehensive real-time statistics and reporting tools.</p>
      </div>
      <div class="benefit-card">
        <div class="benefit-icon">🛒</div>
        <h3>Quality Products</h3>
        <p>Promote our high-converting hilly products that customers love and keep coming back for.</p>
      </div>
      <div class="benefit-card">
        <div class="benefit-icon">💸</div>
        <h3>Weekly Payouts</h3>
        <p>Receive your earnings reliably every Weekly via bank transfer, or Bkash/Nagad/Rocket.</p>
      </div>
      <div class="benefit-card">
        <div class="benefit-icon">🔧</div>
        <h3>Marketing Tools</h3>
        <p>Access ready-to-use banners, product images, and promotional materials to boost your conversions.</p>
      </div>
    </div>
  </section>

  <section class="how-it-works">
    <h2>How It Works</h2>
    <div class="steps">
      <div class="step">
        <div class="step-number">1</div>
        <div class="step-content">
          <h3>Sign Up</h3>
          <p>Fill out our simple application form to join our affiliate program. We'll review your application and get back to you within 24 hours.</p>
        </div>
      </div>
      <div class="step">
        <div class="step-number">2</div>
        <div class="step-content">
          <h3>Get Your Unique Links</h3>
          <p>Once approved, access your personalized dashboard where you can generate custom affiliate links for any product in our store.</p>
        </div>
      </div>
      <div class="step">
        <div class="step-number">3</div>
        <div class="step-content">
          <h3>Promote Products</h3>
          <p>Share your affiliate links on your website, blog, social media channels, email newsletters, or anywhere you have an audience.</p>
        </div>
      </div>
      <div class="step">
        <div class="step-number">4</div>
        <div class="step-content">
          <h3>Track Performance</h3>
          <p>Monitor clicks, conversions, and earnings in real-time through your affiliate dashboard to optimize your marketing strategy.</p>
        </div>
      </div>
      <div class="step">
        <div class="step-number">5</div>
        <div class="step-content">
          <h3>Get Paid</h3>
          <p>Receive your commissions Weekly for all qualified sales. We offer multiple payment methods for your convenience.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- <section class="commission">
    <h2>Commission Structure</h2>
    <div class="commission-rates">
      <div class="rate-card">
        <h3>Standard</h3>
        <div class="rate-value">5%</div>
        <p>On all product categories</p>
        <p>30-day cookie duration</p>
        <p>No minimum payout</p>
      </div>
      <div class="rate-card">
        <h3>Premium</h3>
        <div class="rate-value">20%</div>
        <p>After 10 successful sales</p>
        <p>45-day cookie duration</p>
        <p>Priority support</p>
      </div>
      <div class="rate-card">
        <h3>Elite</h3>
        <div class="rate-value">30%</div>
        <p>After 50 successful sales</p>
        <p>60-day cookie duration</p>
        <p>Custom promotional materials</p>
      </div>
    </div>
  </section> -->


  <footer>
    <div class="footer-links">
      <a href="#">Terms & Conditions</a>
      <a href="#">Privacy Policy</a>
      <a href="#">Affiliate Agreement</a>
      <a href="#">Contact Us</a>
    </div>
    <p>&copy; 2025 YourBrand. All rights reserved.</p>
  </footer>
</body>
</html>
