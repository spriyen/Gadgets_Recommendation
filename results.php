<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smartphones Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        #container {
            display: flex;
        }
        #mobilesResults {
            width: 75%;
            padding: 20px;
        }
        #peopleAlsoPrefer {
            width: 25%;
            padding: 20px;
            background-color: #4169E1; /* Light blue background */
        }
        .phone-container, .record-button {
            background-color: white;
            border: 1px solid #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(65, 105, 225, 0.5);
            margin-bottom: 20px;
            overflow: hidden;
            padding: 10px;
        }
        .phone-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .phone-image {
            margin-right: 10px;
        }
        .phone-details {
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
        <div id="mobilesResults">
            <center><h2>Mobiles Results</h2></center> 
            <!-- Mobiles results will be displayed here -->
        </div>
        <div id="peopleAlsoPrefer">
            <center><h2 style="color: white;">People Also Prefer</h2></center>
            <div id="peopleAlsoPreferContent"></div>
            <!-- Suggestions for other phones can be displayed here -->
        </div>
    </div>

    <script>
        window.onload = function() {
             // Check if 'redirected' is set to 'yes' in sessionStorage
    if (sessionStorage.getItem('redirected') === 'yes') 
        sessionStorage.removeItem('redirected');
            // Display mobile data
            displayMobileData();
            // Display records from session storage that are not empty
            displayNonEmptyRecordsFromSessionStorage();
            // Retrieve selected filter options from session storage
            let storedFilters = sessionStorage.getItem('selectedFilters');

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

        // Display mobile data function
        async function displayMobileData() {
            const mobilesResultsDiv = document.getElementById('mobilesResults');

            try {
                // Fetch mobile data from the local JSON file
                const response = await fetch('smartphones.json');
                const mobileData = await response.json();

                // Check if mobile data is available
                if (mobileData && mobileData.length > 0) {
                    // Iterate over each mobile in the data
                    mobileData.forEach((mobile, index) => {
                        // Create elements to display mobile information
                        const phoneContainer = document.createElement('div');
                        phoneContainer.classList.add('phone-container');
                        phoneContainer.id = `record-${index}`; // Assign unique id
                        phoneContainer.onclick = function() { // Attach onclick event
                            const features = extractFeaturesFromRecord(this);
                            sessionStorage.setItem('selectedPhoneFeatures', JSON.stringify(features));
                            sessionStorage.setItem('redirected', 'yes');
                            window.location.href = './index3.html';
                        };

                        const phoneImage = document.createElement('img');
                        phoneImage.classList.add('phone-image');
                        phoneImage.src = mobile.image;
                        phoneImage.alt = mobile.name;

                        const phoneDetails = document.createElement('div');
                        phoneDetails.classList.add('phone-details');
                        phoneDetails.innerHTML = `
                            <h3>${mobile.name}</h3>
                            <ul>
                                <li><strong>Display:</strong> ${mobile.display}</li>
                                <li><strong>Processor:</strong> ${mobile.processor}</li>
                                <li><strong>RAM:</strong> ${mobile.ram}</li>
                                <li><strong>ROM:</strong> ${mobile.rom}</li>
                                <li><strong>Screen Size:</strong> ${mobile.screen_size}</li>
                                <li><strong>Battery Capacity:</strong> ${mobile.battery_capacity}</li>
                                <li><strong>Front Camera:</strong> ${mobile.front_camera}</li>
                                <li><strong>Back Camera:</strong> ${mobile.back_camera}</li>
                                <li><strong>Cellular:</strong> ${mobile.cellular}</li>
                                <li><strong>Operating System:</strong> ${mobile.operating_system}</li>
                                <li><strong>Price:</strong> ${mobile.price}</li>
                            </ul>
                        `;

                        // Append elements to the container
                        phoneContainer.appendChild(phoneImage);
                        phoneContainer.appendChild(phoneDetails);
                        mobilesResultsDiv.appendChild(phoneContainer);
                    });
                } else {
                    // If no data is available, display a message
                    mobilesResultsDiv.innerHTML = 'No mobile data available.';
                }
            } catch (error) {
                // Handle errors
                console.error('Error fetching mobile data:', error);
                mobilesResultsDiv.innerHTML = 'Error fetching mobile data. Please try again later.';
            }
        }

        // Function to display records from session storage that are not empty
        function displayNonEmptyRecordsFromSessionStorage() {
            const peopleAlsoPreferContent = document.getElementById('peopleAlsoPreferContent');
            const sessionData = sessionStorage.getItem('suggestion');

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
                        recordDetails.classList.add('phone-details');
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
                            sessionStorage.setItem('selectedPhoneFeatures', JSON.stringify(features));
                            sessionStorage.setItem('redirected', 'yes');
                            window.location.href = './index3.html';
                        });
                    }
                });
            } else {
                console.log("No stored data found in session storage.");
            }
        }

        // Function to extract feature values from the clicked record
        function extractFeaturesFromRecord(recordContainer) {
    const featureValues = {};

    // Traverse to find feature details within the record container
    const phoneDetails = recordContainer.querySelector('.phone-details');
    if (phoneDetails) {
        const featureItems = phoneDetails.querySelectorAll('ul li');
        featureItems.forEach(function(item) {
            const parts = item.innerText.split(':'); // Split text by ':'
            if (parts.length === 2) { // Ensure it's in the format 'key: value'
                const featureName = parts[0].trim();
                const featureValue = parts[1].trim();
                featureValues[featureName] = featureValue;
            }
        });
    }

    // Retrieve selected filter options from session storage
    let storedFilters = sessionStorage.getItem('selectedFilters');
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
