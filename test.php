<?php

// Check if a session is already started before calling session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('./partials/header.php');

if (isset($_SESSION['student']) === false) {
    header('Location: index.php');
}

function showFlyingAlert($message, $className)
{
    echo <<<EOT
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var alertDiv = document.createElement("div");
            alertDiv.className = "{$className}";
            alertDiv.innerHTML = "{$message}";
            document.body.appendChild(alertDiv);

            // Triggering reflow to enable animation
            alertDiv.offsetWidth;

            // Add a class to trigger the fly-in animation
            alertDiv.style.left = "10px";

            // Remove the fly-in style after 3 seconds
            setTimeout(function() {
                alertDiv.style.left = "10px";
            }, 2000);

            // Add a class to trigger the fly-out animation after 3 seconds
            setTimeout(function() {
                alertDiv.style.left = "-300px";
            }, 4000);

            // Remove the element after the total duration of the animation (9 seconds)
            setTimeout(function() {
                alertDiv.remove();
            }, 6000);
        });
    </script>
EOT;
}

if (isset($_SESSION['msg'])) {
    $message = $_SESSION['msg'];
    if (stristr($message, "successfully") || stristr($message, "Successfully") || stristr($message, "SUCCESSFUL")) {
        showFlyingAlert($message, "flying-success-alert");
        unset($_SESSION['msg']);
    } else {
        showFlyingAlert($message, "flying-danger-alert");
        unset($_SESSION['msg']);
    }
}

$url = $_SERVER['REQUEST_URI'];

?>

<title>HOSPITALITY MANAGEMENT PROFESSIONAL EXAM</title>

<div class="container">
    <div class="intro">
        <h1>CODEFEST INSTITUTE OF TECHNOLOGY</h1>
        <h2>SILICON VALLEY, CENTENARY CITY</h2>
        <h3>HOSPITALITY MANAGEMENT PROFESSIONAL EXAM</h3>
    </div>

    <form class="quiz" id="quiz-form" method="POST" action="process_form.php">
        <div class="warn">
            Attention! Please note that your exam is scheduled to take place between 8:00 AM and 8:00 PM (12 hours). You will have 45 minutes to answer all the questions. At exactly 8:00 PM, the exam will be taken down from the server. Good luck!
        </div>

        <!-- Timer display -->
        <div class="timer">
            <h3 id="timer"></h3>
        </div>
        <!-- Food Nutrition, Hygiene, and Safety -->

        <!-- Hidden input for course code -->
        <input type="hidden" name="course_code" value="FN">

        <p id="q">1. Which of the following is a basic principle of nutrition?</p>
        <label><input type="radio" name="q1" value="A"> Consuming high amounts of refined sugars</label><br>
        <label><input type="radio" name="q1" value="B"> Balancing macronutrients (carbohydrates, proteins, fats)</label><br>
        <label><input type="radio" name="q1" value="C"> Eating only one type of food group</label><br>
        <label><input type="radio" name="q1" value="D"> Skipping meals regularly</label><br>

        <p id="q">2. What is a primary method for preventing foodborne illnesses?</p>
        <label><input type="radio" name="q2" value="A"> Using unwashed produce</label><br>
        <label><input type="radio" name="q2" value="B"> Maintaining proper hand hygiene</label><br>
        <label><input type="radio" name="q2" value="C"> Cooking food to a minimum temperature</label><br>
        <label><input type="radio" name="q2" value="D"> Storing food in open containers</label><br>

        <p id="q">3. Why is it important to store raw meat separately from other foods?</p>
        <label><input type="radio" name="q3" value="A"> To reduce cooking time</label><br>
        <label><input type="radio" name="q3" value="B"> To prevent cross-contamination</label><br>
        <label><input type="radio" name="q3" value="C"> To increase flavor</label><br>
        <label><input type="radio" name="q3" value="D"> To make it easier to handle</label><br>

        <p id="q">4. Which practice is crucial for maintaining personal hygiene in food handling?</p>
        <label><input type="radio" name="q4" value="A"> Wearing the same gloves for different tasks</label><br>
        <label><input type="radio" name="q4" value="B"> Avoiding handwashing before food preparation</label><br>
        <label><input type="radio" name="q4" value="C"> Regularly washing hands with soap and water</label><br>
        <label><input type="radio" name="q4" value="D"> Using the same towel for drying hands and cleaning surfaces</label>
        <!-- Catering Cost & Calculation -->

        <p id="q">5. What is the purpose of calculating food cost percentages?</p>
        <label><input type="radio" name="q5" value="A"> To determine the profit margin</label><br>
        <label><input type="radio" name="q5" value="B"> To decide on portion sizes</label><br>
        <label><input type="radio" name="q5" value="C"> To set menu prices based on food costs</label><br>
        <label><input type="radio" name="q5" value="D"> To manage staff schedules</label><br>

        <p id="q">6. How can a catering business effectively reduce food waste?</p>
        <label><input type="radio" name="q6" value="A"> Increasing portion sizes</label><br>
        <label><input type="radio" name="q6" value="B"> Regularly analyzing and adjusting menu offerings</label><br>
        <label><input type="radio" name="q6" value="C"> Using excess ingredients in every dish</label><br>
        <label><input type="radio" name="q6" value="D"> Ordering in larger quantities than needed</label><br>

        <p id="q">7. Which of the following is a common technique for controlling catering costs?</p>
        <label><input type="radio" name="q7" value="A"> Reducing the variety of menu items</label><br>
        <label><input type="radio" name="q7" value="B"> Increasing prices across the board</label><br>
        <label><input type="radio" name="q7" value="C"> Minimizing staff training</label><br>
        <label><input type="radio" name="q7" value="D"> Ignoring vendor price changes</label><br>

        <p id="q">8. What should be considered when budgeting for a catering event?</p>
        <label><input type="radio" name="q8" value="A"> Expected weather conditions</label><br>
        <label><input type="radio" name="q8" value="B"> Food and beverage costs</label><br>
        <label><input type="radio" name="q8" value="C"> Event location popularity</label><br>
        <label><input type="radio" name="q8" value="D"> Guest arrival times</label>
        <!-- Introduction to Hospitality Industry -->

        <p id="q">9. Which sector includes both hotels and resorts?</p>
        <label><input type="radio" name="q9" value="A"> Food and Beverage</label><br>
        <label><input type="radio" name="q9" value="B"> Travel and Transportation</label><br>
        <label><input type="radio" name="q9" value="C"> Accommodation</label><br>
        <label><input type="radio" name="q9" value="D"> Retail</label><br>

        <p id="q">10. What is a current trend in the hospitality industry?</p>
        <label><input type="radio" name="q10" value="A"> Decrease in digital marketing</label><br>
        <label><input type="radio" name="q10" value="B"> Increase in eco-friendly practices</label><br>
        <label><input type="radio" name="q10" value="C"> Reduction in guest personalization</label><br>
        <label><input type="radio" name="q10" value="D"> Decline in the use of technology</label><br>

        <p id="q">11. Which factor is crucial for ensuring customer satisfaction in hospitality?</p>
        <label><input type="radio" name="q11" value="A"> Offering minimal services</label><br>
        <label><input type="radio" name="q11" value="B"> Ignoring guest feedback</label><br>
        <label><input type="radio" name="q11" value="C"> Providing excellent and personalized service</label><br>
        <label><input type="radio" name="q11" value="D"> Maintaining a high level of staff turnover</label><br>

        <p id="q">12. What type of career opportunities exist within the hospitality industry?</p>
        <label><input type="radio" name="q12" value="A"> Only front-line service roles</label><br>
        <label><input type="radio" name="q12" value="B"> Various roles including management and operational positions</label><br>
        <label><input type="radio" name="q12" value="C"> Exclusive to culinary positions</label><br>
        <label><input type="radio" name="q12" value="D"> Limited to administrative roles</label>
        <!-- Introduction to Hotel Billing and Accounting -->

        <p id="q">13. What is the main function of a Property Management System (PMS) in hotels?</p>
        <label><input type="radio" name="q13" value="A"> To manage social media accounts</label><br>
        <label><input type="radio" name="q13" value="B"> To track reservations, room assignments, and billing</label><br>
        <label><input type="radio" name="q13" value="C"> To handle marketing campaigns</label><br>
        <label><input type="radio" name="q13" value="D"> To control staff scheduling</label><br>

        <p id="q">14. Which document summarizes the financial performance of a hotel?</p>
        <label><input type="radio" name="q14" value="A"> Guest Folio</label><br>
        <label><input type="radio" name="q14" value="B"> Daily Revenue Report</label><br>
        <label><input type="radio" name="q14" value="C"> Profit and Loss Statement</label><br>
        <label><input type="radio" name="q14" value="D"> Inventory Report</label><br>

        <p id="q">15. What is a key component of effective revenue management?</p>
        <label><input type="radio" name="q15" value="A"> Fixed pricing for all room types</label><br>
        <label><input type="radio" name="q15" value="B"> Dynamic pricing based on demand and supply</label><br>
        <label><input type="radio" name="q15" value="C"> Ignoring seasonal demand changes</label><br>
        <label><input type="radio" name="q15" value="D"> Consistent pricing for all guests</label><br>

        <p id="q">16. What is the primary purpose of hotel billing systems?</p>
        <label><input type="radio" name="q16" value="A"> To manage marketing expenses</label><br>
        <label><input type="radio" name="q16" value="B"> To process guest transactions and manage accounts</label><br>
        <label><input type="radio" name="q16" value="C"> To schedule staff shifts</label><br>
        <label><input type="radio" name="q16" value="D"> To order hotel supplies</label>
        <!-- Food Nutrition, Hygiene, and Safety (Advanced) -->

        <p id="q">17. What is a safe food storage practice?</p>
        <label><input type="radio" name="q17" value="A"> Keeping all food at room temperature</label><br>
        <label><input type="radio" name="q17" value="B"> Storing food items based on their type and use</label><br>
        <label><input type="radio" name="q17" value="C"> Mixing raw and cooked foods together</label><br>
        <label><input type="radio" name="q17" value="D"> Using damaged or unsealed containers</label><br>

        <p id="q">18. Which of the following is a common cause of foodborne illnesses?</p>
        <label><input type="radio" name="q18" value="A"> Properly cooked food</label><br>
        <label><input type="radio" name="q18" value="B"> Cross-contamination between raw and cooked foods</label><br>
        <label><input type="radio" name="q18" value="C"> Using clean utensils</label><br>
        <label><input type="radio" name="q18" value="D"> Following hygiene protocols</label>
        <!-- Catering Cost & Calculation (Advanced) -->

        <p id="q">19. What method is often used to calculate the cost of a menu item?</p>
        <label><input type="radio" name="q19" value="A"> Subtracting ingredient costs from the menu price</label><br>
        <label><input type="radio" name="q19" value="B"> Adding food cost, labor, and overhead costs</label><br>
        <label><input type="radio" name="q19" value="C"> Estimating costs based on market trends</label><br>
        <label><input type="radio" name="q19" value="D"> Using a fixed markup on all items</label><br>

        <p id="q">20. How can catering businesses improve financial management?</p>
        <label><input type="radio" name="q20" value="A"> Ignoring supplier price increases</label><br>
        <label><input type="radio" name="q20" value="B"> Regularly reviewing and adjusting budgets</label><br>
        <label><input type="radio" name="q20" value="C"> Reducing investment in quality ingredients</label><br>
        <label><input type="radio" name="q20" value="D"> Avoiding detailed cost analysis</label><br>
        <br>

        <p id="q">21. What is the primary purpose of blanching vegetables before freezing?</p>

        <input type="radio" id="q21a" name="q21" value="A"> <label for="q21a"> To add flavor</label> <br>
        <input type="radio" id="q21b" name="q21" value="B"> <label for="q21b"> To kill bacteria</label> <br>
        <input type="radio" id="q21c" name="q21" value="C"> <label for="q21c"> To preserve color and nutrients</label> <br>
        <input type="radio" id="q21d" name="q21" value="D"> <label for="q21d"> To soften the texture</label> <br>


        <p id="q">22. Which of the following is considered a moist cooking method?</p>
        <input type="radio" id="q22a" name="q22" value="A"> <label for="q22a"> Grilling</label> <br>
        <input type="radio" id="q22b" name="q22" value="B"> <label for="q22b"> Sautéing</label> <br>
        <input type="radio" id="q22c" name="q22" value="C"> <label for="q22c"> Boiling</label> <br>
        <input type="radio" id="q22d" name="q22" value="D"> <label for="q22d"> Roasting</label> <br>

        <p id="q">23. What is the correct procedure for measuring dry ingredients in food production?</p>
        <input type="radio" id="q23a" name="q23" value="A"> <label for="q23a"> Scoop and level off with a straight edge</label> <br>
        <input type="radio" id="q23b" name="q23" value="B"> <label for="q23b"> Shake the measuring cup until full</label> <br>
        <input type="radio" id="q23c" name="q23" value="C"> <label for="q23c"> Pack ingredients tightly into the cup</label> <br>
        <input type="radio" id="q23d" name="q23" value="D"> <label for="q23d"> Fill the cup halfway and estimate</label> <br>

        <p id="q">24. Which method is best for ensuring even cooking of meats?</p>
        <input type="radio" id="q24a" name="q24" value="A"> <label for="q24a"> Using high heat throughout</label> <br>
        <input type="radio" id="q24b" name="q24" value="B"> <label for="q24b"> Turning the meat frequently</label> <br>
        <input type="radio" id="q24c" name="q24" value="C"> <label for="q24c"> Cooking at low heat for an extended period</label> <br>
        <input type="radio" id="q24d" name="q24" value="D"> <label for="q24d"> Using a meat thermometer to monitor internal temperature</label> <br>

        <!-- Food & Beverage Service -->
        <p id="q">25. What is the main responsibility of a sommelier?</p>
        <input type="radio" id="q25a" name="q25" value="A"> <label for="q25a"> Managing the kitchen staff</label> <br>
        <input type="radio" id="q25b" name="q25" value="B"> <label for="q25b"> Pairing wine with food</label> <br>
        <input type="radio" id="q25c" name="q25" value="C"> <label for="q25c"> Serving appetizers to guests</label> <br>
        <input type="radio" id="q25d" name="q25" value="D"> <label for="q25d"> Supervising housekeeping operations</label> <br>

        <p id="q">26. Which of the following is considered a table service style?</p>
        <input type="radio" id="q26a" name="q26" value="A"> <label for="q26a"> Buffet</label> <br>
        <input type="radio" id="q26b" name="q26" value="B"> <label for="q26b"> Counter service</label> <br>
        <input type="radio" id="q26c" name="q26" value="C"> <label for="q26c"> Russian service</label> <br>
        <input type="radio" id="q26d" name="q26" value="D"> <label for="q26d"> Drive-through service</label> <br>

        <p id="q">27. What is a key aspect of good customer service in a restaurant?</p>
        <input type="radio" id="q27a" name="q27" value="A"> <label for="q27a"> Serving food as quickly as possible, regardless of quality</label> <br>
        <input type="radio" id="q27b" name="q27" value="B"> <label for="q27b"> Addressing guests by name and providing personalized service</label> <br>
        <input type="radio" id="q27c" name="q27" value="C"> <label for="q27c"> Avoiding eye contact with guests to focus on tasks</label> <br>
        <input type="radio" id="q27d" name="q27" value="D"> <label for="q27d"> Taking orders without suggesting additional items</label> <br>

        <p id="q">28. Which type of glass is typically used for serving red wine?</p>
        <input type="radio" id="q28a" name="q28" value="A"> <label for="q28a"> Champagne flute</label> <br>
        <input type="radio" id="q28b" name="q28" value="B"> <label for="q28b"> Highball glass</label> <br>
        <input type="radio" id="q28c" name="q28" value="C"> <label for="q28c"> Burgundy glass</label> <br>
        <input type="radio" id="q28d" name="q28" value="D"> <label for="q28d"> Shot glass</label> <br>

        <!-- Accommodation Management -->
        <p id="q">29. What is the primary function of the front office department in a hotel?</p>
        <input type="radio" id="q29a" name="q29" value="A"> <label for="q29a"> Managing food and beverage services</label> <br>
        <input type="radio" id="q29b" name="q29" value="B"> <label for="q29b"> Overseeing housekeeping</label> <br>
        <input type="radio" id="q29c" name="q29" value="C"> <label for="q29c"> Handling guest reservations and check-ins</label> <br>
        <input type="radio" id="q29d" name="q29" value="D"> <label for="q29d"> Managing maintenance and repairs</label> <br>

        <p id="q">30. What is a key component of housekeeping in a hotel?</p>
        <input type="radio" id="q30a" name="q30" value="A"> <label for="q30a"> Preparing meals for guests</label> <br>
        <input type="radio" id="q30b" name="q30" value="B"> <label for="q30b"> Maintaining cleanliness and order in guest rooms</label> <br>
        <input type="radio" id="q30c" name="q30" value="C"> <label for="q30c"> Organizing guest activities</label> <br>
        <input type="radio" id="q30d" name="q30" value="D"> <label for="q30d"> Managing guest reservations</label> <br>

        <p id="q">31. Which type of guest prefers an early check-in and late check-out?</p>
        <input type="radio" id="q31a" name="q31" value="A"> <label for="q31a"> Leisure travelers</label> <br>
        <input type="radio" id="q31b" name="q31" value="B"> <label for="q31b"> Business travelers</label> <br>
        <input type="radio" id="q31c" name="q31" value="C"> <label for="q31c"> Solo travelers</label> <br>
        <input type="radio" id="q31d" name="q31" value="D"> <label for="q31d"> Budget travelers</label> <br>

        <p id="q">32. What is the primary objective of revenue management in hotels?</p>
        <input type="radio" id="q32a" name="q32" value="A"> <label for="q32a"> Reducing employee turnover</label> <br>
        <input type="radio" id="q32b" name="q32" value="B"> <label for="q32b"> Maximizing room occupancy and revenue</label> <br>
        <input type="radio" id="q32c" name="q32" value="C"> <label for="q32c"> Minimizing housekeeping costs</label> <br>
        <input type="radio" id="q32d" name="q32" value="D"> <label for="q32d"> Enhancing guest loyalty programs</label> <br>

        <!-- Tourism -->
        <p id="q">33. What is the primary motivation for most tourists to travel?</p>
        <input type="radio" id="q33a" name="q33" value="A"> <label for="q33a"> Work-related purposes</label> <br>
        <input type="radio" id="q33b" name="q33" value="B"> <label for="q33b"> Leisure and relaxation</label> <br>
        <input type="radio" id="q33c" name="q33" value="C"> <label for="q33c"> Business conferences</label> <br>
        <input type="radio" id="q33d" name="q33" value="D"> <label for="q33d"> Medical treatment</label> <br>

        <p id="q">34. What is the role of a tour operator in the tourism industry?</p>
        <input type="radio" id="q34a" name="q34" value="A"> <label for="q34a"> Operating transportation services</label> <br>
        <input type="radio" id="q34b" name="q34" value="B"> <label for="q34b"> Booking accommodations for tourists</label> <br>
        <input type="radio" id="q34c" name="q34" value="C"> <label for="q34c"> Creating and selling holiday packages</label> <br>
        <input type="radio" id="q34d" name="q34" value="D"> <label for="q34d"> Conducting guided city tours</label> <br>

        <p id="q">35. Which of the following is a cultural tourist attraction?</p>
        <input type="radio" id="q35a" name="q35" value="A"> <label for="q35a"> National parks</label> <br>
        <input type="radio" id="q35b" name="q35" value="B"> <label for="q35b"> Historic sites and monuments</label> <br>
        <input type="radio" id="q35c" name="q35" value="C"> <label for="q35c"> Amusement parks</label> <br>
        <input type="radio" id="q35d" name="q35" value="D"> <label for="q35d"> Beach resorts</label> <br>

        <p id="q">36. What is a key factor in sustainable tourism development?</p>
        <input type="radio" id="q36a" name="q36" value="A"> <label for="q36a"> Maximizing short-term profits</label> <br>
        <input type="radio" id="q36b" name="q36" value="B"> <label for="q36b"> Minimizing environmental impact</label> <br>
        <input type="radio" id="q36c" name="q36" value="C"> <label for="q36c"> Expanding tourist facilities</label> <br>
        <input type="radio" id="q36d" name="q36" value="D"> <label for="q36d"> Increasing visitor numbers</label> <br>

        <!-- Event Planning -->
        <p id="q">37. What is the first step in planning an event?</p>
        <input type="radio" id="q37a" name="q37" value="A"> <label for="q37a"> Booking a venue</label> <br>
        <input type="radio" id="q37b" name="q37" value="B"> <label for="q37b"> Defining the event's purpose and objectives</label> <br>
        <input type="radio" id="q37c" name="q37" value="C"> <label for="q37c"> Sending out invitations</label> <br>
        <input type="radio" id="q37d" name="q37" value="D"> <label for="q37d"> Creating a budget</label> <br>

        <p id="q">38. What is an essential element of successful event management?</p>
        <input type="radio" id="q38a" name="q38" value="A"> <label for="q38a"> Offering the lowest possible prices</label> <br>
        <input type="radio" id="q38b" name="q38" value="B"> <label for="q38b"> Ensuring effective communication and coordination</label> <br>
        <input type="radio" id="q38c" name="q38" value="C"> <label for="q38c"> Providing free giveaways</label> <br>
        <input type="radio" id="q38d" name="q38" value="D"> <label for="q38d"> Selecting exotic locations</label> <br>

        <p id="q">39. What is the role of a Master of Ceremonies (MC) at an event?</p>
        <input type="radio" id="q39a" name="q39" value="A"> <label for="q39a"> Handling technical equipment</label> <br>
        <input type="radio" id="q39b" name="q39" value="B"> <label for="q39b"> Coordinating catering services</label> <br>
        <input type="radio" id="q39c" name="q39" value="C"> <label for="q39c"> Engaging the audience and facilitating program flow</label> <br>
        <input type="radio" id="q39d" name="q39" value="D"> <label for="q39d"> Managing security</label> <br>

        <p id="q">40. Which factor is most critical when choosing an event venue?</p>
        <input type="radio" id="q40a" name="q40" value="A"> <label for="q40a"> Proximity to tourist attractions</label> <br>
        <input type="radio" id="q40b" name="q40" value="B"> <label for="q40b"> The venue's size and capacity</label> <br>
        <input type="radio" id="q40c" name="q40" value="C"> <label for="q40c"> Availability of parking spaces</label> <br>
        <input type="radio" id="q40d" name="q40" value="D"> <label for="q40d"> The venue's historical significance</label> <br>

        <!-- Food and Beverage Management -->
        <p id="q">41. What is a standard method for controlling food costs in a restaurant?</p>
        <input type="radio" id="q41a" name="q41" value="A"> <label for="q41a"> Using the most expensive ingredients available</label> <br>
        <input type="radio" id="q41b" name="q41" value="B"> <label for="q41b"> Implementing portion control measures</label> <br>
        <input type="radio" id="q41c" name="q41" value="C"> <label for="q41c"> Reducing menu options</label> <br>
        <input type="radio" id="q41d" name="q41" value="D"> <label for="q41d"> Increasing portion sizes</label> <br>

        <p id="q">42. What is the role of a sommelier in a fine dining restaurant?</p>
        <input type="radio" id="q42a" name="q42" value="A"> <label for="q42a"> Cooking gourmet dishes</label> <br>
        <input type="radio" id="q42b" name="q42" value="B"> <label for="q42b"> Serving appetizers</label> <br>
        <input type="radio" id="q42c" name="q42" value="C"> <label for="q42c"> Recommending and serving wines</label> <br>
        <input type="radio" id="q42d" name="q42" value="D"> <label for="q42d"> Managing the cash register</label> <br>

        <p id="q">43. Which of the following is considered a front-of-house position in a restaurant?</p>
        <input type="radio" id="q43a" name="q43" value="A"> <label for="q43a"> Chef</label> <br>
        <input type="radio" id="q43b" name="q43" value="B"> <label for="q43b"> Dishwasher</label> <br>
        <input type="radio" id="q43c" name="q43" value="C"> <label for="q43c"> Server</label> <br>
        <input type="radio" id="q43d" name="q43" value="D"> <label for="q43d"> Line cook</label> <br>

        <p id="q">4. What is the importance of menu engineering in a restaurant?</p>
        <input type="radio" id="q44a" name="q44" value="A"> <label for="q44a"> To increase the variety of dishes offered</label> <br>
        <input type="radio" id="q44b" name="q44" value="B"> <label for="q44b"> To optimize profitability and sales of menu items</label> <br>
        <input type="radio" id="q44c" name="q44" value="C"> <label for="q44c"> To reduce ingredient costs</label> <br>
        <input type="radio" id="q44d" name="q44" value="D"> <label for="q44d"> To attract more kitchen staff</label> <br>

        <p id="q">45. What does FIFO stand for in inventory management?</p>
        <input type="radio" id="q45a" name="q45" value="A"> <label for="q45a"> First In, First Out</label> <br>
        <input type="radio" id="q45b" name="q45" value="B"> <label for="q45b"> Fast In, Fast Out</label> <br>
        <input type="radio" id="q45c" name="q45" value="C"> <label for="q45c"> Free In, Free Out</label> <br>
        <input type="radio" id="q45d" name="q45" value="D"> <label for="q45d"> First In, Full Out</label> <br>

        <!-- Travel Agency Management -->
        <p id="q">46. What is a primary responsibility of a travel agent?</p>
        <input type="radio" id="q46a" name="q46" value="A"> <label for="q46a"> Arranging travel itineraries and bookings</label> <br>
        <input type="radio" id="q46b" name="q46" value="B"> <label for="q46b"> Flying planes</label> <br>
        <input type="radio" id="q46c" name="q46" value="C"> <label for="q46c"> Operating hotels</label> <br>
        <input type="radio" id="q46d" name="q46" value="D"> <label for="q46d"> Managing cruise ships</label> <br>

        <p id="q">47. Which of the following is a key skill for a successful travel agent?</p>
        <input type="radio" id="q47a" name="q47" value="A"> <label for="q47a"> Cooking gourmet meals</label> <br>
        <input type="radio" id="q47b" name="q47" value="B"> <label for="q47b"> Technical writing</label> <br>
        <input type="radio" id="q47c" name="q47" value="C"> <label for="q47c"> Effective communication and customer service</label> <br>
        <input type="radio" id="q47d" name="q47" value="D"> <label for="q47d"> Designing fashion accessories</label> <br>

        <p id="q">48. Which system is commonly used to manage reservations, billing, and guest information in hotels?</p>
        <input type="radio" id="q48a" name="q48" value="A"> <label for="q48a"> Property Management System (PMS)</label> <br>
        <input type="radio" id="q48b" name="q48" value="B"> <label for="q48b"> Customer Relationship Management (CRM)</label> <br>
        <input type="radio" id="q48c" name="q48" value="C"> <label for="q48c"> Point of Sale (POS) System</label> <br>
        <input type="radio" id="q48d" name="q48" value="D"> <label for="q48d"> Inventory Management System (IMS)</label> <br>
        <br>

        <!-- Manual submit button (quiz will auto-submit when time runs out) -->
        <button type="button" onclick="submitQuiz(false)">SUBMIT TEST</button>
    </form>

    <div id="result"></div>
</div>

<script>
    function submitQuiz(autoSubmit = false) {
        const form = document.getElementById('quiz-form');
        const formData = new FormData(form);
        const answeredQuestions = new Map();
        const unansweredQuestions = {};
        const courseCode = form.querySelector('input[name="course_code"]').value;

        // Collect all radio inputs
        const inputs = form.querySelectorAll('input[type="radio"]');
        const questionNames = new Set();

        inputs.forEach(input => {
            questionNames.add(input.name); // Add unique question names
        });

        // Iterate over all unique question names
        questionNames.forEach(name => {
            const selectedInput = form.querySelector(`input[name="${name}"]:checked`);
            if (selectedInput) {
                answeredQuestions.set(name, selectedInput.value);
            } else {
                unansweredQuestions[name] = 'NONE'; // Mark unanswered questions
            }
        });

        // If not all questions are answered and it's a manual submit
        if (!autoSubmit && answeredQuestions.size < questionNames.size) {
            if (!confirm("You haven't answered all questions. Do you want to submit anyway?")) {
                return; // Prevent submission if user decides not to submit
            }
        }

        // If all questions are answered, confirm if the user wants to submit
        if (!autoSubmit && answeredQuestions.size === questionNames.size) {
            if (!confirm("You have answered all questions. Are you sure you want to submit?")) {
                return; // Prevent submission if user decides not to submit
            }
        }

        // Add unanswered questions to the form data as a hidden input
        const unansweredField = document.createElement('input');
        unansweredField.type = 'hidden';
        unansweredField.name = 'unanswered';
        unansweredField.value = JSON.stringify(unansweredQuestions);
        form.appendChild(unansweredField);

        // Combine answered and unanswered into formData
        formData.forEach((value, key) => {
            answeredQuestions.set(key, value);
        });

        // Prepare form data for submission
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'process_form.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                window.location.href = 'test_submitted.php'; // Redirect upon success
            }
        };

        // Include the course code in the submitted data
        answeredQuestions.set('course_code', courseCode);

        xhr.send(JSON.stringify(Object.fromEntries(answeredQuestions)));
    }

    // Timer function
    function startTimer() {
        let timeLimit = 45 * 60; // Time in seconds (e.g., 60 seconds = 1 minute)
        const timerElement = document.getElementById('timer');

        const updateTimer = () => {
            if (timeLimit <= 0) {
                alert("Time's up! Submitting your quiz.");
                submitQuiz(true); // Auto-submit when time is up
                return;
            }

            const minutes = Math.floor(timeLimit / 60);
            const seconds = timeLimit % 60;
            timerElement.textContent = `Time left: 0hrs : ${minutes}min : ${seconds < 10 ? '0' + seconds : seconds}sec`;

            timeLimit--;
            setTimeout(updateTimer, 1000); // Update the timer every second
        };

        updateTimer(); // Start the timer
    }

    window.onload = startTimer; // Start timer when the page loads
</script>

<?php include('./partials/footer.php') ?>