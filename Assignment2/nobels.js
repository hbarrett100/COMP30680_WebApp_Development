/* define global variables */

var prizesByYear;
var winnersByID;

/*parse in JSON files using code provided in lecture*/
var xmlhttp1 = new XMLHttpRequest();
var url = "prizesByYear.json";

xmlhttp1.onreadystatechange = function () {
    if (xmlhttp1.readyState == 4 && xmlhttp1.status == 200) {
        prizesByYear = JSON.parse(xmlhttp1.responseText);
        dropdown(prizesByYear);
    }
};

xmlhttp1.open("GET", url, true);
xmlhttp1.send();


var xmlhttp2 = new XMLHttpRequest();
var url = "winnersByID.json";

xmlhttp2.onreadystatechange = function () {
    if (xmlhttp2.readyState == 4 && xmlhttp2.status == 200) {
        winnersByID = JSON.parse(xmlhttp2.responseText);
    }
};

xmlhttp2.open("GET", url, true);
xmlhttp2.send();

/* function for creating dropdowns */

function dropdown(prizesByYear) {

    var prizes = prizesByYear.prizes;

    var optionsHtml = '';
    var array = []; /*creating an empty array to put the category names from the JSON */
    optionsHtml += "<option>All categories</option>";
    for (var i = 0; i < prizes.length; i++) {
        if (array.includes(prizes[i].category) == false) {
            /*checking array does not already include category - if it is not already present, add it to the array*/
            var category = prizes[i].category;
            array.push(category)
            optionsHtml += "<option>" + category + "</option>";
            /*categories not hard-coded in as per assignment requirement*/
        }
    }

    document.getElementById("category").innerHTML = optionsHtml;


    /*dropdown for year start selection*/

    var optionsHtml_2 = '';
    var yearStart;

    optionsHtml_2 += "<option>Choose Year</option>";
    for (var i = 1970; i <= 2018; i++) {
        yearStart = i;
        optionsHtml_2 += "<option>" + yearStart + "</option>";
    }

    document.getElementById("start").innerHTML = optionsHtml_2;

    /*dropdown for year end  selection*/

    var optionsHtml_3 = '';
    var yearEnd;

    optionsHtml_3 += "<option>Choose Year</option>";

    for (var i = 1970; i <= 2018; i++) {
        yearEnd = i;
        optionsHtml_3 += "<option>" + yearEnd + "</option>";
    }

    document.getElementById("end").innerHTML = optionsHtml_3;
}

// function for error handling dropdown selections. This will check if years are appropriately
//selected from dropdowns and will either display an error message or call the function to display the table.

function errorCheck() {

    var yearStart = document.getElementById("start");
    var yearEnd = document.getElementById("end");
    var error = document.getElementById("error");
    var errorText = '';
    var yearStartFilter = yearStart.options[yearStart.selectedIndex].text;
    var yearEndFilter = yearEnd.options[yearEnd.selectedIndex].text;

    if (yearStartFilter === "Choose Year" || yearEndFilter === "Choose Year") {

        errorText += 'Error. You must select a range of years before clicking submit.';
        error.innerHTML = errorText;
        error.style.display = "block";
        //        console.log('error1')

    } else if (yearEndFilter < yearStartFilter) {
        errorText += 'Error. Year to must be greater than Year from';
        error.innerHTML = errorText;
        error.style.display = "block";
        //        console.log('error2');

    } else {
        error.style.display = "none";
        displayPrizes(); /*if no errors, then call the function to display table results.*/
    }
}


/* function to display results in table. Called by errorCheck function when no errors present */

function displayPrizes() {

    var prizes = prizesByYear.prizes;

    var Info = "<table border = 1 id = 'tableId'>"; /*create table*/
    var firstname, surname, id, gender, year, category, motivation;

    var yearStart = document.getElementById("start");
    var yearStartFilter = yearStart.options[yearStart.selectedIndex].text; /*to get content from dropdown as per w3schools*/

    var yearEnd = document.getElementById("end");
    var yearEndFilter = yearEnd.options[yearEnd.selectedIndex].text; /*to get content from dropdown*/

    var catDropdown = document.getElementById("category");
    var categoryFilter = catDropdown.options[catDropdown.selectedIndex].text; /*to get content from dropdown*/

    Info += "<tr><th>Firstname</th><th>Surname</th><th>Year</th><th>Category</th><th>Motivation</th><th>Gender</th><th></th></tr>";
    for (var i = 0; i < prizes.length; i++) {
        year = prizes[i].year;
        category = prizes[i].category;

        if ((category === categoryFilter || categoryFilter === "All categories") &&
            (year >= yearStartFilter && year <= yearEndFilter)) {

            for (var j = 0; j < prizes[i].laureates.length; j++) {
                firstname = prizes[i].laureates[j].firstname;
                surname = prizes[i].laureates[j].surname;
                motivation = prizes[i].laureates[j].motivation;
                if (motivation === undefined) {
                    motivation = "No motivation provided"; /*if no motivation given, print "No motivation provided". This is to prevent 'undefined' from being printed in the table. */
                }

                id = prizes[i].laureates[j].id;
                gender = checkId(id); /*calling checkID function which compares id in both JSON files and returns the gender*/
                Info += "<tr><td>" + firstname + "</td><td>" + surname + "</td><td>" + year + "</td><td>" + category + "</td><td>" + motivation + "</td><td>" + gender + "</td><td><button onclick = 'moreInfo(" + id + ")'>More Information</button></td></tr>";

            }

        }
    }
    Info += "</table>";
    /*adding table to div in html*/
    document.getElementById("table").innerHTML = Info;

    /*display radio buttons for gender filtering once submit button is clicked. This allows further filering options for part 2 of assignment*/

    var radio = document.getElementById("radioButtons");
    radio.style.display = "block";

    /* hide more-info div each time a new range of years is selected*/

    var moreInfo = document.getElementById("moreInfo");

    if (moreInfo.style.display == "block") {
        moreInfo.style.display = "none";
    }

    document.getElementById("all").checked = true; /*all winners radio button checked each time a new range of years is selected*/
}

/*function to check if id in both JSON files match and return gender. Will take id from prizesByYear as an argument. */

function checkId(idToCheck) {

    var laureates = winnersByID.laureates;
    var id;

    for (var i = 0; i < laureates.length; i++) {
        id = laureates[i].id;
        if (idToCheck == id) {
            return laureates[i].gender;
        }
    }
}

/* function to filter gender adapted from w3schools. https://www.w3schools.com/howto/howto_js_filter_table.asp */

function filterGender() {
    var input, filter, table, tr, td, i, txtValue;

    input = document.querySelector('input[name="gender"]:checked').value; /*to get value from radio button selection*/
    filter = input.toUpperCase();
    table = document.getElementById("tableId"); /*the id assigned to my table of result*/
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[5]; /*gender is in fifth column*/
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) === 0 || input === "all") {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none"; /*show or hide the genders*/
            }
        }
    }

    /*hide more info each time a new gender selection is made*/
    var moreInfo = document.getElementById("moreInfo");

    if (moreInfo.style.display == "block") {
        moreInfo.style.display = "none";
    }
}

/* function to get moreInfo on each winner*/
function moreInfo(idForInfo) {

    var laureates = winnersByID.laureates;
    var id, born, died, bornCity, motivation, affiliation, city, country, category;

    /*check if id in both JSONs match and then iterate through WinnersByYear to get extra info for each winner*/

    for (var i = 0; i < laureates.length; i++) {
        id = laureates[i].id;
        if (idForInfo == id) {
            born = laureates[i].born;
            if (born === "0000-00-00") {
                /*to prevent "0000-00-00 being displayed */
                born = "Not applicable";
            }
            died = laureates[i].died;
            if (died === "0000-00-00") {
                /*to prevent "0000-00-00 being displayed */
                died = "Not applicable";
            }
            bornCity = laureates[i].bornCity;
            if (bornCity === undefined) {
                /*if no city of birth given, print "Not applicable". This is to prevent 'undefined' from being printed in the table. */
                bornCity = "Not applicable";
            }

            for (var j = 0; j < laureates[i].prizes.length; j++) {
                category = laureates[i].prizes[j].category;
                motivation = laureates[i].prizes[j].motivation;
                if (motivation === undefined) {
                    /*if no motivation given, print "No motivation provided". This is to prevent 'undefined' from being printed in the table. */
                    motivation = "No motivation provided";
                }

                for (var x = 0; x < laureates[i].prizes[j].affiliations.length; x++) {
                    affiliation = laureates[i].prizes[j].affiliations[x].name;
                    if (affiliation === undefined) {
                        /*if no affiliation given, print "Not applicable". This is to prevent 'undefined' from being printed in the table. */
                        affiliation = "Not applicable";
                    }
                    city = laureates[i].prizes[j].affiliations[x].city;
                    if (city === undefined) {
                        /*if no affiliation given, print "Not applicable". This is to prevent 'undefined' from being printed in the table. */
                        city = "Not applicable";
                    }
                    country = laureates[i].prizes[j].affiliations[x].country;
                    if (country === undefined) {
                        /*if no affiliation given, print "Not applicable". This is to prevent 'undefined' from being printed in the table. */
                        country = "Not applicable";
                    }


                    /*add the extra info into an object*/
                    var moreInfoObj = {
                        born: born,
                        died: died,
                        cityOfBirth: bornCity,
                        category: category,
                        motivation: motivation,
                        affiliationName: affiliation,
                        affiliationCity: city,
                        affiliationCountry: country
                    }


                    var moreInfo = document.getElementById("moreInfo");

                    /*add contents of objects to div in html*/

                    moreInfo.innerHTML = "Born: " + moreInfoObj.born + "<br>Died: " + moreInfoObj.died + "<br>City of birth: " + moreInfoObj.cityOfBirth + "<br>Category: " + moreInfoObj.category + "<br>Motivation: " + moreInfoObj.motivation + "<br>Name of affiliation: " + moreInfoObj.affiliationName + "<br>City of affiliation: " + moreInfoObj.affiliationCity + "<br>Country of affiliation: " + moreInfoObj.affiliationCountry;

                    /*display the div in html*/
                    moreInfo.style.display = "block";

                }

            }
        }
    }
}
