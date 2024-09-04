<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laptops Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        #container {
            display: flex;
        }
        #laptopsResults {
            width: 75%;
            padding: 20px;
        }
        #peopleAlsoPrefer {
            width: 25%;
            padding: 20px;
            background-color: #4169E1; /* Light blue background */
            color: white;
        }
        .laptop-container, .record-button {
            background-color: white;
            border: 1px solid #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(65, 105, 225, 0.5);
            margin-bottom: 20px;
            overflow: hidden;
            padding: 10px;
        }
        .laptop-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .laptop-image {
            margin-right: 10px;
        }
        .laptop-details {
            overflow: hidden;
        }
        .record-button {
            width: 100%;
            text-align: left;
            border: none;
            cursor: pointer;
        }
        .record-button:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div id="container">
        <div id="laptopsResults">
            <center><h2>Laptops Results</h2></center> 
            <!-- Laptop results will be displayed here -->
        </div>
        <div id="peopleAlsoPrefer">
            <center><h2>People Also Prefer</h2></center>
            <div id="peopleAlsoPreferContent"></div>
            <!-- Suggestions for other laptops can be displayed here -->
        </div>
    </div>

    <script>
        window.onload = function() {
            // Display laptop data
            displayLaptopData();
        };

        // Display laptop data function
        async function displayLaptopData() {
            const laptopsResultsDiv = document.getElementById('laptopsResults');

            try {
                // Fetch laptop data from the local JSON file
                const response = await fetch('laptops_data.json');
                const laptopData = await response.json();

                // Check if laptop data is available
                if (laptopData && laptopData.length > 0) {
                    // Iterate over each laptop in the data
                    laptopData.forEach((laptop, index) => {
                        // Create elements to display laptop information
                        const laptopContainer = document.createElement('div');
                        laptopContainer.classList.add('laptop-container');
                        laptopContainer.id = `record-${index}`; // Assign unique id
                        laptopContainer.onclick = function() { // Attach onclick event
                            const features = extractFeaturesFromRecord(this);
                            sessionStorage.setItem('selectedLaptopFeatures', JSON.stringify(features));
                            sessionStorage.setItem('redirected', 'yes');
                            window.location.href = 'index3.php';
                        };

                        const laptopImage = document.createElement('img');
                        laptopImage.classList.add('laptop-image');
                        const imageUrls = laptop.Image.split('|');
                        laptopImage.src = imageUrls.length > 0 ? imageUrls[0] : ''; // Use the first URL
                        laptopImage.alt = laptop.Name;

                        const laptopDetails = document.createElement('div');
                        laptopDetails.classList.add('laptop-details');
                        
                        // Build the details list dynamically based on non-null values
                        let detailsHTML = `<h3>${laptop.Name}</h3><ul>`;
                        Object.entries(laptop).forEach(([key, value]) => {
                            if (value !== null && key !== 'Image') {
                                detailsHTML += `<li><strong>${key}:</strong> ${value}</li>`;
                            }
                        });
                        detailsHTML += `</ul>`;
                        laptopDetails.innerHTML = detailsHTML;

                        // Append elements to the container
                        laptopContainer.appendChild(laptopImage);
                        laptopContainer.appendChild(laptopDetails);
                        laptopsResultsDiv.appendChild(laptopContainer);
                    });
                } else {
                    // If no data is available, display a message
                    laptopsResultsDiv.innerHTML = 'No laptop data available.';
                }
            } catch (error) {
                // Handle errors
                console.error('Error fetching laptop data:', error);
                laptopsResultsDiv.innerHTML = 'Error fetching laptop data. Please try again later.';
            }
        }
        window.onload = function() {
    // Check if 'redirected' is set to 'yes' in sessionStorage
    if (sessionStorage.getItem('redirected') === 'yes') {
        sessionStorage.removeItem('redirected');
    }

    // Display laptop data
    displayLaptopData();

    // Display records from session storage that are not empty
    displayNonEmptyRecordsFromSessionStorage();

    // Retrieve selected filter options from session storage
    let storedFilters = sessionStorage.getItem('lap_selected_features');

    // Check if storedFilters is not null or undefined
    if (storedFilters) {
        // Parse the stored filter options from JSON format
        storedFilters = JSON.parse(storedFilters);

        // Loop through each filter option and log it
        Object.keys(storedFilters).forEach(function(filter) {
            console.log(filter + ': ' + storedFilters[filter]);
        });
    } else {
        console.log("No selected filters found in session storage.");
    }
};

function displayNonEmptyRecordsFromSessionStorage() {
    const peopleAlsoPreferContent = document.getElementById('peopleAlsoPreferContent');
    const sessionData = sessionStorage.getItem('lap_suggestion'); // Assuming lap_suggestions stores laptop records

    if (sessionData) {
        const data = JSON.parse(sessionData);
        const seenRecords = new Set(); // Set to track unique records

        Object.entries(data).forEach(([recordId, recordContent]) => {
            const recordString = JSON.stringify(recordContent); // Convert record to string for comparison
            if (Object.keys(recordContent).length !== 0 && !seenRecords.has(recordString)) {
                seenRecords.add(recordString); // Add record to set if it's unique

                const recordButton = document.createElement('button'); // Create button element
                recordButton.classList.add('record-button'); // Add a class for styling

                const recordDetails = document.createElement('div');
                recordDetails.classList.add('laptop-details');
                recordDetails.innerHTML = `
                    <ul>
                        ${Object.entries(recordContent).map(([key, value]) => `<li><strong>${key}:</strong> ${value}</li>`).join('')}
                    </ul>
                `;

                recordButton.appendChild(recordDetails);
                peopleAlsoPreferContent.appendChild(recordButton);

                // Attach click event handler to each button
                recordButton.addEventListener('click', function(event) {
                    event.stopPropagation(); // Prevent click event from propagating to the parent container
                    const features = extractFeaturesFromRecord(this, recordDetails); // Pass the button contents as parameter
                    sessionStorage.setItem('lap_selected_filters', JSON.stringify(features));
                    sessionStorage.setItem('redirected', 'yes');
                    window.location.href = './lapmain.html'; // Redirect to another page
                });
            }
        });
    } else {
        console.log("No stored data found in session storage.");
    }
}

// Function to extract feature values from the clicked record
function extractFeaturesFromRecord(recordButton, recordDetails) {
    const featureValues = {};

    // Extract features from the provided record content
    const featureItems = recordDetails.querySelectorAll('li');
    featureItems.forEach(function(item) {
        const parts = item.innerText.split(':'); // Split text by ':'
        if (parts.length === 2) { // Ensure it's in the format 'key: value'
            const featureName = parts[0].trim();
            const featureValue = parts[1].trim();
            featureValues[featureName] = featureValue;
        }
    });

    // Retrieve selected filter options from session storage
    let storedFilters = sessionStorage.getItem('lap_selected_features');
    if (storedFilters) {
        storedFilters = JSON.parse(storedFilters);

        // Add non-null filter values to featureValues
        Object.keys(storedFilters).forEach(function(filter) {
            if (storedFilters[filter]) {
                featureValues[filter] = storedFilters[filter];
            }
        });
    }

    return featureValues;
}
    </script>
</body>
</html>
