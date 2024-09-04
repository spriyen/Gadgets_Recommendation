document.addEventListener('DOMContentLoaded', function () {
    let phones = [];
    let laptops = [];
    let phoneIndex = 0;
    let laptopIndex = 0;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch_data.php', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    console.log('Fetched data:', data); 
                    phones = data.phones;
                    laptops = data.laptops;

                    if (phones.length > 0) {
                        displayProduct('phone', phones[phoneIndex]);
                    } else {
                        console.error('No phones data available');
                    }

                    if (laptops.length > 0) {
                        displayProduct('laptop', laptops[laptopIndex]);
                    } else {
                        console.error('No laptops data available');
                    }
                } catch (error) {
                    console.error('Error parsing JSON data:', error);
                }
            } else {
                console.error('Error fetching data:', xhr.statusText);
            }
        }
    };
    xhr.send();

    function displayProduct(type, product) {
        if (product) {
            const imageElem = document.getElementById(`${type}-image`);
            const nameElem = document.getElementById(`${type}-name`);

            imageElem.src = product.image;
            nameElem.textContent = product.name;
        } else {
            console.error(`No product data available for ${type}`);
        }
    }

    function slideImage(type, direction) {
        const imageElem = document.getElementById(`${type}-image`);
        imageElem.style.transform = `translateX(${direction * 100}%)`;
        setTimeout(() => {
            imageElem.style.transform = `translateX(0)`;
            if (type === 'phone') {
                phoneIndex = (phoneIndex + direction + phones.length) % phones.length;
                displayProduct('phone', phones[phoneIndex]);
            } else {
                laptopIndex = (laptopIndex + direction + laptops.length) % laptops.length;
                displayProduct('laptop', laptops[laptopIndex]);
            }
        }, 500);
    }

    window.nextPhone = function() {
        slideImage('phone', 1);
    }

    window.prevPhone = function() {
        slideImage('phone', -1);
    }

    window.nextLaptop = function() {
        slideImage('laptop', 1);
    }

    window.prevLaptop = function() {
        slideImage('laptop', -1);
    }
});
