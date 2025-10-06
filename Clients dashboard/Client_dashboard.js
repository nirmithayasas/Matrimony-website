// Client_dashboard.js
function viewProfile() { alert('View Profile feature.'); }
function bookAppointment() { alert('Booking appointment feature.'); }
function viewDocuments() { alert('View Documents feature.'); }
function contactSupport() { alert('Contact Counselor feature.'); }
function makePayment() { alert('Make Payment feature.'); }
function viewResources() { alert('View Resources feature.'); }
function viewDocument(docType) { alert(`Opening ${docType} document.`); }

setInterval(() => {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => card.style.animation = 'pulse 0.5s ease');
    setTimeout(() => statCards.forEach(card => card.style.animation = ''), 500);
}, 30000);
