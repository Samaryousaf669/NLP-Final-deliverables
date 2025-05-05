How to Run the Project:
1. Install and Locate the XAMPP Folder
--Once you’ve downloaded and installed XAMPP, the XAMPP folder will be automatically placed in the C drive (e.g., C:\xampp).
--Open this folder and navigate to the htdocs directory.

2. Add Your Source Code
--Copy your project folder (e.g., src) and paste it into the htdocs folder.

3. Start Apache and MySQL
--Launch the XAMPP Control Panel.
--Click "Start" for both Apache and MySQL.
--When both services are highlighted in green, it means they are running successfully.

4. Open the Project in a Browser
--Open any web browser.
--In the address bar, type:   "localhost/src/ "

However, the project won’t run yet because the database is not set up.

5. Create the Database
--In your browser, go to: "localhost/phpmyadmin/"
--Click on the "New" option in the left sidebar.
--Enter the database name: " restaurant_chatbot-sameer"
--Click the "Create" button.

6. Import the Database Schema
--After creating the database, select "restaurant_chatbot-sameer" from the left sidebar.
--Go to the "Import" tab from the top menu.
--Click "Browse" and choose the .sql file from your schema folder.
--Click "Import" to upload the file into the database.

7. Run the Project Again
--Now that your server and database are properly configured, return to your browser.
--Enter the following URL again: : "localhost/src/"


--------------Your project should now run successfully!----------------------------
