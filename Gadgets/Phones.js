document.getElementById('processor1').addEventListener('input', function() {
    const inputValue = this.value.toLowerCase();
    fetch('../processors.json')
        .then(response => response.json())
        .then(data => {
            const filteredProcessors = data.filter(item => item.processor.toLowerCase().includes(inputValue)).slice(0, 10);
            const processorList = document.getElementById('processorList');
            processorList.innerHTML = '';
            filteredProcessors.forEach(item => {
                const processorItem = document.createElement('div');
                processorItem.textContent = item.processor;
                processorItem.classList.add('processor-item');
                processorItem.addEventListener('click', function() {
                    document.getElementById('processor1').value = item.processor;
                    processorList.innerHTML = '';
                });
                processorList.appendChild(processorItem);
            });
        })
        .catch(error => {
            console.error('Error fetching processors:', error);
        });
}); 
function applyFilters() {
    const form = document.getElementById('searchForm');
    const formData = new FormData(form);
    const queryString = new URLSearchParams(formData).toString();
    
    fetch(`http://localhost/Gadgets%20recommendation/search.php?${queryString}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                console.log('Filtered data has been saved to smartphones.json');
                openPage();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    let selectedFilters = {};
    for (const [key, value] of formData.entries()) {
        if (key == 'os')
            selectedFilters['operating_system'] = value;
        else
            selectedFilters[key] = value;
    }
    console.log(selectedFilters);
    sessionStorage.setItem('selectedFilters', JSON.stringify(selectedFilters));
    fetch(`http://localhost/Gadgets%20recommendation/process_filters.php?${queryString}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(selectedFilters)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Matching data from search history:');
        console.log("FetchedData ", data);
        const additionalFeatures = {};
        data.forEach(record => {
            const recordFeatures = {};
            for (const key in record) {
                if (!selectedFilters[key] && record[key] !== null && record[key] !== "") {
                    recordFeatures[key] = record[key];
                }
            }
            additionalFeatures[`Record ${data.indexOf(record) + 1}`] = recordFeatures;
        });
        console.log('Additional Features:');
        console.log(additionalFeatures);
        const jsonData = JSON.stringify(additionalFeatures);
        sessionStorage.setItem('suggestion', jsonData);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}   
function sendDataToSearchPHP(formData) {
    fetch('http://localhost/Gadgets%20recommendation/search.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Data received from search.php:', data);
        if (data && data.length > 0) {
            openPage();
        } else {
            console.log('No matching data found.');
        }
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}   
function openPage() {
    var url = 'http://localhost/Gadgets%20recommendation/results.php';
    window.location.href = url;
}
window.onload = function() {
if (sessionStorage.getItem('redirected') === 'yes') {
    
    const selectedFeatures = sessionStorage.getItem('selectedPhoneFeatures');
    
    if (selectedFeatures) {
        const features = JSON.parse(selectedFeatures);
        for (const [key, value] of Object.entries(features)) {
            const element = document.getElementById(key);
            if (element) {
                if (element.tagName === 'SELECT') {
                    if ([...element.options].some(option => option.value === value)) {
                        element.value = value;
                    }
                } else if (element.tagName === 'INPUT' && element.type === 'text') {
                    element.value = value;
                }
            }
        }
        
    }
    let b = document.getElementsByTagName("button")[0];
    b.click();
    sessionStorage.removeItem('redirected');
    }
};