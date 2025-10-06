function showPage(pageName) {
    const pages = document.querySelectorAll('.page');
    pages.forEach(page => page.classList.remove('active'));
    document.getElementById(pageName).classList.add('active');
    window.scrollTo(0, 0);
}

function handleSubmit(event) {
    event.preventDefault();
    alert('Thank you for your message! We will get back to you soon.');
    event.target.reset();
}