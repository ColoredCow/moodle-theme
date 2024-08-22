document.addEventListener('DOMContentLoaded', function() {
    // Initialize the tabs
    const tabs = document.querySelectorAll('#tabs ul li a');
    const contents = document.querySelectorAll('#tabs > div');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(event) {
            tabs.forEach(t => t.parentNode.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));

            tab.parentNode.classList.add('active');
            document.querySelector(tab.getAttribute('href')).classList.add('active');
        });
    });
});
