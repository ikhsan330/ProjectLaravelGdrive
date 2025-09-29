document.addEventListener('DOMContentLoaded', () => {

    // --- 1. Sidebar Functionality ---
    const sidebarButtons = document.querySelectorAll('.sidebar-button');
    const sidebarContents = document.querySelectorAll('.sidebar-content');

    sidebarButtons.forEach(button => {
        button.addEventListener('click', () => {
            sidebarButtons.forEach(btn => {
                btn.classList.remove('sidebar-button-active');
                btn.classList.add('sidebar-button-inactive');
            });
            button.classList.add('sidebar-button-active');
            button.classList.remove('sidebar-button-inactive');

            sidebarContents.forEach(content => {
                content.classList.add('hidden');
            });

            const target = document.querySelector(button.dataset.target);
            if (target) {
                target.classList.remove('hidden');
            }
        });
    });

    // --- 2. Scroll Reveal Animation ---
    const reveal = () => {
        const reveals = document.querySelectorAll('.reveal');
        for (let i = 0; i < reveals.length; i++) {
            const windowHeight = window.innerHeight;
            const elementTop = reveals[i].getBoundingClientRect().top;
            const elementVisible = 150; // Distance from bottom of screen to start animation

            if (elementTop < windowHeight - elementVisible) {
                reveals[i].classList.add('active');
            } else {
                reveals[i].classList.remove('active'); // Optional: remove to re-animate on scroll up
            }
        }
    };
    window.addEventListener('scroll', reveal);
    reveal(); // Initial check on load

    // --- 3. Counter Animation ---
    const counters = document.querySelectorAll('.counter');
    const speed = 200; // The lower the #, the faster the count

    const animateCounters = () => {
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const inc = Math.ceil(target / speed);

                if (count < target) {
                    counter.innerText = Math.min(count + inc, target);
                    setTimeout(updateCount, 10);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        });
    };

    // Intersection Observer to trigger counter animation only when visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target); // Stop observing after animation runs once
            }
        });
    }, { threshold: 0.5 }); // Trigger when 50% of the element is visible

    // Observe the first counter element's parent (the stats section)
    if (counters.length > 0) {
        observer.observe(counters[0].parentElement.parentElement.parentElement);
    }
});
