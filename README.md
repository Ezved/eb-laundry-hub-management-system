<img width="1850" height="945" alt="image" src="https://github.com/user-attachments/assets/28799de0-ce94-4888-95e6-910d02d5b875" /><br>E&B Laundry Hub System
<br>A modern web-based laundry management system designed to streamline booking, order tracking, and service operations for E&B Laundry Hub. The system provides a seamless experience for both customers and administrators, supporting pickup & delivery scheduling, order summaries, and real-time order management.

## Features

<img width="1865" height="944" alt="image" src="https://github.com/user-attachments/assets/ce530b1b-c4d8-495b-bcce-61e7d88497c4" /><br>User Side
- Schedule Pickup & Delivery  
- Order Summary with detailed breakdown  
- Payment Options (COD & GCash)  
- Order Tracking  
- Rescheduling  

<img width="1863" height="944" alt="image" src="https://github.com/user-attachments/assets/49635483-1919-424c-9392-b3396229220c" /><br>Admin Side
- Dashboard Management  
- Order Processing  
- Capacity Control

## Tech Stack
| Layer        | Technology                               |
| ------------ | ---------------------------------------- |
| Backend      | Laravel                                  |
| Frontend     | HTML, CSS, JavaScript                    |
| UI Framework | Bootstrap                                |
| Database     | MySQL                                    |
| Styling      | Custom CSS (Glass UI, Responsive Design) |
| Mail         | Laravel Mail (SMTP)                      |

## Project Structure
- E&B-Laundry-Hub/
- │── app/
- │   ├── Http/Controllers/
- │   │   └── OrderController.php
- │── resources/
- │   ├── views/
- │   │   ├── user/dashboard/
- │   │   ├── admin/
- │── public/
- │   ├── css/
- │   ├── images/
- │── routes/
- │   └── web.php
- │── database/
- │── .env

## Installation
1. Clone the repository
<br> git clone https://github.com/your-username/eb-laundry-hub.git
2. Navigate to project
<br> cd eb-laundry-hub
3. Install dependencies
<br> composer install
<br> npm install
4. Setup environment
<br> cp .env.example .env
<br> php artisan key:generate
5. Configure database in .env
6. Run migrations
<br> php artisan migrate
7. Start server
<br> php artisan serve

## Usage

Follow these steps to use the system:

### For Users

- Register or log in to your account  
- Schedule a laundry pickup by filling out the booking form  
- Select service type, add-ons, and preferred schedule  
- Review the Order Summary before confirming  
- Choose a payment method (Cash on Delivery or GCash)  
- Confirm booking and wait for email confirmation  
- Track your order status in the dashboard  

### For Admin

- Log in to the admin dashboard  
- View all incoming orders  
- Update order status (Pending → Ongoing → Completed)  
- Manage customer orders and history  
- Monitor daily laundry capacity (30 loads limit)   

## Future Improvements

- Online payment integration (GCash API)  
- SMS notifications  
- Mobile app version  
- AI-based load estimation  
- Admin analytics dashboard
- Inventory 

## Developer

**E&B Laundry Hub System** was designed and developed by:

**Lourrence Ed Senajon & Wesley Crabajales**  
BS Computer Science – 3rd Year  
Ateneo de Davao University  

## Acknowledgement

Special thanks to:

- E&B Laundry Hub  
- Project advisers and instructors  

  ## This project will be deployed soon
