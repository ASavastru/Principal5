let nav = 0;
let params = new URLSearchParams(document.location.search);
let insertedDate = params.get("insertedDate");
document.getElementById('insertedDate2').setAttribute('value', insertedDate);
//Tracks clicks for prevMonth and nextMonth with increments

let clicked = null;
//receives date of day clicked by user

let appointments = localStorage.getItem('appointments') ?JSON.parse(localStorage.getItem('appointments')) : [];
// array of appointment objects that exists in localstorage
// JSON.parse turns appointments into a string so they can be stored in localstorage
// it either returns an array of appointment objects or an empty array

const calendar = document.getElementById('calendar');
const newAppointmentModal = document.getElementById('newAppointmentModal');
const deleteAppointmentModal = document.getElementById('deleteAppointmentModal');
const backDrop = document.getElementById('modalBackDrop');
const appointmentTitleInput = document.getElementById('appointmentTitleInput');
// these constants are declared globally because they're used everywhere
// they reference to ids

const weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
// the weekdays never change therefore they can be constant
// this array helps determine padding days

function load() {
    const dt = new Date();
    // current date is assigned to dt

    if (nav !== 0) {
        dt.setMonth(new Date().getMonth() + nav);
    }
    // since dt determines what month/year is shown,
    // changing month value by adding nav to it changes the
    // month displayed

    const day = dt.getDate();
    const month = dt.getMonth();
    const year = dt.getFullYear();
    // easier to work with simple constants

    const daysInMonth = new Date(year, month+1, 0).getDate();
    // month+1 because "month" is kind of like an index value
    // a.k.a. jan === 0, jul === 6, dec === 11
    // but date === 0 and this gives us the date of the
    // last date of the previous month in relation to month+1.
    //.getDate stringifies it

    const firstDayOfMonth = new Date(year, month, 1);
    // adds first day of a given month to this constant which
    // allows me to easily access the actual weekday in order to
    // correctly display the start of the month and, by extension,
    // the entirety of the month

    const dateString = firstDayOfMonth.toLocaleDateString('en-UK', {
        weekday: 'long', //gives me name of day linked to date
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
    });
    // constructs dateString object that makes calculating paddingDays easy
    // toLocaleDateString() method returns a string with the date

    const paddingDays = weekdays.indexOf(dateString.split(', ')[0]);
    // padding days represent the beginning days of a week that are not
    // a part of the month that is viewed by the user at a given moment.
    // .split(', ')[0] returns ONLY the weekday linked to the specific
    // date of the first day of month

    document.getElementById('monthDisplay').innerText =
        `${dt.toLocaleDateString('en-UK', {month: 'long'})} ${year}`;
    // sends month and year to html in order to be displayed in the page

    calendar.innerHTML = '';
    // whenever load() is called but before
    // constructing the calendar, the calendar clears
    // a.k.a. all squares, padding or daySquare

    for(let i = 1; i <= paddingDays + daysInMonth; i++) {
        // i <= paddingDays + daysInMonth because
        // we have to render the padding days too

        const daySquare = document.createElement('div');
        // for every single iteration we create a div

        daySquare.classList.add('day');
        // adds day number to div

        const dayString = `${i - paddingDays}/${month + 1}/${year}`;
        const momentDayString = `${year}-${month+1}-${i - paddingDays}`;

        if (i > paddingDays) {
            // logic that determines if
            // a padding day needs to be rendered
            // or a daySquare

            daySquare.innerText = i - paddingDays;
            // renders the day in the div

            daySquare.setAttribute("data-date", moment(momentDayString).format('YYYY-MM-DD'));

            const appointmentForDay = appointments.find(e => e.date === dayString);
            // checks if there is an appointment in the day
            // and adds it to appointmentForDay

            if ( i - paddingDays === day && nav === 0 ) {
                // daySquare.id = 'currentDay';
            }
            // checks for current day and applies styling to it

            if ( insertedDate == daySquare.getAttribute('data-date')){
                daySquare.id = 'currentDay';
            }

            if(appointmentForDay) {
                const appointmentDiv = document.createElement('div');

                // these two lines add the pink bookmark on days with appointments
                // appointmentDiv.classList.add('appointment');
                // appointmentDiv.innerText = appointmentForDay.title;
                daySquare.appendChild(appointmentDiv);
            }
            // if there is one,
            // shows appointment

            daySquare.addEventListener('click', () => {
                // openModal(dayString);
                let x = moment(momentDayString).format('YYYY-MM-DD');
                document.getElementById('insertedDate').value = x;
                document.getElementById('getDataFromDatabase').submit();
            });
            // listens for clicks on each div
            // and calls openModal for the specific date clicked

        } else {
            daySquare.classList.add('padding');
            // this makes sure that it doesn't have the
            // same style as a daySquare
        }
        calendar.appendChild(daySquare);
    }
}

ajaxAxios(insertedDate);

function ajaxAxios(date) {
    document.getElementById('makeAppointment').addEventListener('click', e => {
        const data = new FormData();
        data.set("insertedDate", moment(date).format('YYYY-MM-DD'))
        data.set("locationFilter", document.getElementById('locationDropdown').value)
        data.set('_token', document.getElementById('token').value)
        axios.post("/makeAppointment", data).then(function(response){
            console.log(response.data)
        })
    })
}

function openModal(date) {
    // function that opens the modal

    clicked = date;
    // ref: line 5

    const appointmentForDay = appointments.find(e => e.date === clicked);
    // checks if there is a saved appointment in the clicked day

    if (appointmentForDay){
        document.getElementById('appointmentText').innerText = appointmentForDay.title;
        // shows appointment text a.k.a. whatever the use inputted

        deleteAppointmentModal.style.display = 'block';
    } else {
        newAppointmentModal.style.display = 'block';
        // changes style of this modal a.k.a. shows it
    }

    backDrop.style.display = 'block';
    // changes style of backDrop a.k.a. shows it
}

function closeModal() {
    newAppointmentModal.style.display = 'none';
    deleteAppointmentModal.style.display = 'none';
    backDrop.style.display = 'none';
    appointmentTitleInput.value = '';
    clicked = null;
    // closes/clears/resets everything related to the modal

    load();
}

function saveAppointment() {
    if (appointmentTitleInput.value) {
        // if there is a value typed into the input

        appointmentTitleInput.classList.remove('error');
        // removes error style if there was any

        appointments.push({
            date: clicked,
            title: appointmentTitleInput.value,
        });
        // pushes an object into appointments array

        localStorage.setItem('appointments', JSON.stringify(appointments));
        // re-stores appointments array stringified

        closeModal();
    } else {
        appointmentTitleInput.classList.add('error');
        // adds error styling
    }
}

function deleteAppointment() {
    appointments = appointments.filter(e => e.date !== clicked);
    // filters out clicked appointment

    localStorage.setItem('appointments', JSON.stringify(appointments));
    // resets appointments in localStorage
    closeModal();
}

function initButtons() {
    document.getElementById("nextButton").addEventListener('click', () => {
        nav++;
        load();
    });
    // increments nav on nextButton click and reloads page to display next month

    document.getElementById("backButton").addEventListener('click', () => {
        nav--;
        load();
    });
    // decrements nav on backButton click and reloads page to display previous month

    document.getElementById('saveButton').addEventListener('click', saveAppointment);

    document.getElementById('cancelButton').addEventListener('click', () => {
        appointmentTitleInput.classList.remove('error');
        //eliminates error style when cancelButton isClicked
        closeModal();
    });

    document.getElementById('locationDropdown').addEventListener('click', function (){
        document.getElementById('locationFilterGet').value = document.getElementById('locationDropdown').value;
    });
    //gives location filter from GET form the value from the dropdown so it can be used to filter shown appointments

    document.getElementById('deleteButton').addEventListener('click', deleteAppointment);

    document.getElementById('closeButton').addEventListener('click', closeModal);

}

load(); // loads calendar
initButtons(); // initializes buttons