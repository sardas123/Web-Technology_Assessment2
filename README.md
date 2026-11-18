# Online Registration Form

A simple web application for online student registration, built as a college project.
The user fills in a form, and on clicking **Register** the details are validated and
displayed on the same page in a neatly formatted table, with a registration ID and a Print option.

## Technologies Used

| Technology | Purpose |
|------------|---------|
| HTML5 | Structure of the form and page |
| CSS3 | Styling, layout, print styles |
| JavaScript | Client-side form validation |
| jQuery (3.7.1, CDN) | DOM handling and AJAX request |
| PHP | Server-side validation and generating the result |

## Features

- Responsive registration form (name, email, phone, date of birth, gender, course, interests, address)
- Client-side validation with inline error messages (JavaScript/jQuery)
- Server-side validation and input sanitising (PHP, `htmlspecialchars`) to prevent XSS
- Form submitted with AJAX, so the page does not reload
- Formatted result table with success banner and auto-generated registration ID
- Print button (print layout hides the form)
- Entire project contained in a single file

## Project Structure

```
registration.php   -> HTML + CSS + JavaScript/jQuery + PHP (all in one file)
README.md          -> project documentation
```

## Requirements

- A PHP server: XAMPP / WAMP / MAMP, or PHP 7.4+ installed
- A modern web browser (Chrome, Edge, Firefox)
- Internet connection (jQuery is loaded from a CDN)

## How to Run

### Option 1: XAMPP

1. Install XAMPP from https://www.apachefriends.org
2. Open the XAMPP Control Panel and click **Start** next to **Apache**
3. Copy `registration.php` into `C:\xampp\htdocs\project\` (create the folder if needed)
4. Open `http://localhost/project/registration.php` in your browser
5. Fill in the form and click **Register**

### Option 2: PHP built-in server

```bash
cd path/to/project-folder
php -S localhost:8000
```

Then open `http://localhost:8000/registration.php`

> Note: double-clicking the file will not work, because PHP must run on a server.

## How It Works

1. The browser loads `registration.php`, which shows the form.
2. On **Register**, jQuery validates the fields. Any errors appear in red under the field.
3. If valid, jQuery sends the data to the same file using `$.ajax()` (POST).
4. PHP validates and sanitises the data again, creates a registration ID, and returns JSON containing formatted HTML.
5. jQuery inserts that HTML below the form and clears the form.

## Validation Rules

| Field | Rule |
|-------|------|
| Full name | Letters, spaces and dots only; minimum 3 characters |
| Email | Valid email format |
| Phone | 10 digits, starting with 6, 7, 8 or 9 |
| Date of birth | Required |
| Gender | One option must be selected |
| Course | Must be selected |
| Address | Minimum 10 characters |
| Confirmation | Checkbox must be ticked |

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Apache will not start | Another program is using port 80 (e.g. Skype, IIS). Close it or change Apache's port to 8080 and open `http://localhost:8080/...` |
| "Could not reach the server" message | Make sure Apache is running and the page is opened through `http://localhost/...`, not `file://` |
| Page shows PHP code as text | The file is not being run by a PHP server; check it is inside `htdocs` |
| Form has no styling issues but validation does nothing | No internet; jQuery CDN failed to load |

## Possible Future Enhancements

- Store registrations in a MySQL database
- Photo / document upload
- Admin page to list and search registrations
- Email confirmation to the student
- Export the result as PDF

## Author

Name: ______________________
Roll No: ____________________
College: ____________________
Year / Department: ____________
