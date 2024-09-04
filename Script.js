document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const phoneFilter = document.getElementById('phoneFilter');
    const laptopFilter = document.getElementById('laptopFilter');

    // Function to set the filter based on the device type
    function setFilter(deviceType) {
        if (deviceType === 'phone') {
            phoneFilter.style.display = 'block';
            laptopFilter.style.display = 'none';
            form.querySelector('input[value="phone"]').checked = true;
        } else if (deviceType === 'laptop') {
            phoneFilter.style.display = 'none';
            laptopFilter.style.display = 'block';
            form.querySelector('input[value="laptop"]').checked = true;
        }
    }

    // Check URL parameters to set the filter on page load
    const urlParams = new URLSearchParams(window.location.search);
    const deviceType = urlParams.get('deviceType') || 'phone';  // default to phone if not set
    setFilter(deviceType);

    // Change event for filter form
    form.addEventListener('change', function() {
        const selectedValue = form.querySelector('input[name="deviceType"]:checked').value;
        setFilter(selectedValue);
    });
});