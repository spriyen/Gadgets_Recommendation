document.getElementById('processor2').addEventListener('input', function() {
    const inputValue = this.value.toLowerCase();

    fetch('../lap_processors.json')
        .then(response => response.json())
        .then(data => {
            const processorsArray = data.processors;
            const filteredProcessors = processorsArray
                .filter(processor => processor.toLowerCase().includes(inputValue))
                .slice(0, 10);

            const processorList = document.getElementById('processorList1');
            processorList.innerHTML = '';

            filteredProcessors.forEach(processor => {
                const processorItem = document.createElement('div');
                processorItem.textContent = processor;
                processorItem.classList.add('processor-item');
                processorItem.addEventListener('click', function() {
                    document.getElementById('processor2').value = processor;
                    processorList.innerHTML = '';
                });
                processorList.appendChild(processorItem);
            });
        })
        .catch(error => {
            console.error('Error fetching processors:', error);
        });
});

function applyFiltersLaptop() {
    const form = document.getElementById('searchForm');
    const formData = new FormData(form);
    const queryString = new URLSearchParams(formData).toString();

    fetch(`../lap_search.php?${queryString}`)
        .then(response => response.json())
        .then(data => {
            console.log('Raw data:', data);
            if (data.length > 0) {
                console.log('Filtered data:', data);
                displayLaptops(data);
                window.location.href = '../lap_results.php';
            } else {
                console.log('No data found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });

    const ramSize = document.getElementById('ram_size').value;
    const ramType = document.getElementById('ram_type').value;
    const internalStorage = document.getElementById('internal_storage').value;
    const processor = document.getElementById('processor2').value;
    const cores = document.getElementById('cores').value;
    const os = document.getElementById('os').value;
    const screenSize = document.getElementById('screenSize').value;
    const gpuBrand = document.getElementById('gpuBrand').value;
    const price = document.getElementById('price').value;

    const selectedFilters = {
        ramSize: ramSize,
        ramType: ramType,
        internalStorage: internalStorage,
        processor: processor,
        cores: cores,
        os: os,
        screenSize: screenSize,
        gpuBrand: gpuBrand,
        price: price
    };

    sessionStorage.setItem('lap_selected_features', JSON.stringify(selectedFilters));
    fetchAndProcessData(selectedFilters);
}

function displayLaptops(laptops) {
    const laptopsList = document.getElementById('laptopsList');
    laptopsList.innerHTML = '';

    laptops.forEach(laptop => {
        const laptopDiv = document.createElement('div');
        laptopDiv.classList.add('laptop');

        Object.keys(laptop).forEach(key => {
            if (laptop[key] !== null && laptop[key] !== '') {
                const laptopDetail = document.createElement('p');
                laptopDetail.textContent = `${key}: ${laptop[key]}`;
                laptopDiv.appendChild(laptopDetail);
            }
        });

        laptopsList.appendChild(laptopDiv);
    });
}

function fetchAndProcessData(selectedFilters) {
    const allFeatures = ['cores', 'gpuBrand', 'internalStorage', 'os', 'price', 'processor', 'ramSize', 'ramType', 'screensize'];

    const selectedFeatures = Object.keys(selectedFilters)
        .filter(key => selectedFilters[key] !== null && selectedFilters[key] !== "");

    fetch(`../lap_process_filters.php`, {
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
            console.log(data);

            const additionalFeatures = {};

            data.forEach((record, index) => {
                const recordFeatures = {};

                for (const key in record) {
                    if (!selectedFeatures.includes(key) && record[key] !== null && record[key] !== "") {
                        recordFeatures[key] = record[key];
                    }
                }

                if (Object.keys(recordFeatures).length > 0) {
                    additionalFeatures[`Record ${index + 1}`] = recordFeatures;
                }
            });

            console.log('Additional Features:');
            console.log(additionalFeatures);

            const jsonData = JSON.stringify(additionalFeatures);

            sessionStorage.setItem('lap_suggestion', jsonData);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

window.onload = function() {
    if (sessionStorage.getItem('redirected') === 'yes') {
        const selectedFeatures = sessionStorage.getItem('lap_selected_filters');

        if (selectedFeatures) {
            const features = JSON.parse(selectedFeatures);

            for (const [key, value] of Object.entries(features)) {
                let elementId = key;
                if (key === 'screen_size') {
                    elementId = 'screenSize';
                } else if (key === 'gpu') {
                    elementId = 'gpuBrand';
                }

                const element = document.getElementById(elementId);
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

        let button = document.getElementsByTagName("button")[0];
        button.click();

        sessionStorage.removeItem('redirected');
    }
};
