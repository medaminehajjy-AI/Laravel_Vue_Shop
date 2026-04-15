<template>
  <div class="about-page">
    <div class="about-hero">
      <h1>About Mol-Lmli7Shop</h1>
      <p>Your trusted online store for quality products</p>
    </div>
    
    <div class="about-content">
      <div class="section">
        <div class="icon-wrapper">
          <ShoppingBag :size="40" />
        </div>
        <h2>Our Mission</h2>
        <p>At MiniShop, we believe in providing the best quality products at the most affordable prices. Our mission is to make online shopping simple, secure, and enjoyable for everyone.</p>
      </div>
      
      <div class="section">
        <div class="icon-wrapper">
          <Truck :size="40" />
        </div>
        <h2>Fast Delivery</h2>
        <p>We partner with reliable shipping providers to ensure your orders reach you safely and quickly. Most orders are delivered within 3-5 business days.</p>
      </div>
      
      <div class="section">
        <div class="icon-wrapper">
          <ShieldCheck :size="40" />
        </div>
        <h2>Secure Payments</h2>
        <p>Your payment information is always protected with industry-standard encryption. We accept multiple payment methods for your convenience.</p>
      </div>
      
      <div class="section">
        <div class="icon-wrapper">
          <Headphones :size="40" />
        </div>
        <h2>24/7 Support</h2>
        <p>Our dedicated support team is available around the clock to help you with any questions or concerns. Don't hesitate to reach out!</p>
      </div>
    </div>
    
    <div class="team-section">
      <h2>Why Choose Us?</h2>
      <div class="features-grid">
        <div class="feature">
          <CheckCircle :size="24" />
          <span>Quality Products</span>
        </div>
        <div class="feature">
          <CheckCircle :size="24" />
          <span>Best Prices</span>
        </div>
        <div class="feature">
          <CheckCircle :size="24" />
          <span>Easy Returns</span>
        </div>
        <div class="feature">
          <CheckCircle :size="24" />
          <span>Customer Satisfaction</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ShoppingBag, Truck, ShieldCheck, Headphones, CheckCircle } from 'lucide-vue-next';

export default {
  name: 'About',
  components: { ShoppingBag, Truck, ShieldCheck, Headphones, CheckCircle }
}
</script>

<style scoped>
.about-page {
  max-width: 1200px;
  margin: 0 auto;
}

.about-hero {
  text-align: center;
  padding: 60px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 16px;
  margin-bottom: 40px;
}

.about-hero h1 {
  font-size: 2.5rem;
  margin: 0 0 10px 0;
}

.about-hero p {
  font-size: 1.2rem;
  margin: 0;
  opacity: 0.9;
}

.about-content {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
  margin-bottom: 40px;
}

@media (max-width: 768px) {
  .about-content {
    grid-template-columns: 1fr;
  }
}

.section {
  background: white;
  padding: 32px;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
}

.section:hover {
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.icon-wrapper {
  width: 72px;
  height: 72px;
  background: linear-gradient(135deg, #667eea22 0%, #764ba222 100%);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #667eea;
  margin-bottom: 20px;
}

.section h2 {
  margin: 0 0 12px 0;
  color: #1a1a2e;
  font-size: 1.3rem;
}

.section p {
  margin: 0;
  color: #6b7280;
  line-height: 1.6;
}

.team-section {
  background: white;
  padding: 40px;
  border-radius: 16px;
  border: 1px solid #f0f0f0;
  text-align: center;
}

.team-section h2 {
  margin: 0 0 30px 0;
  color: #1a1a2e;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

@media (max-width: 768px) {
  .features-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.feature {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 20px;
  background: #f9fafb;
  border-radius: 12px;
  color: #667eea;
}

.feature span {
  color: #1a1a2e;
  font-weight: 500;
}
</style>
