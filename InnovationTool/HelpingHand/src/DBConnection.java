import java.sql.*;
import java.util.Scanner;

public class DBConnection {

	// Parameter für Verbindungsaufbau
	// JDBC driver name and database URL
	static final String JDBC_DRIVER = "com.mysql.jdbc.Driver";
	static final String DB_URL = "jdbc:mysql://localhost/";


	@SuppressWarnings("resource")
	public Connection getDatabaseConnection() throws ClassNotFoundException, SQLException {
		Connection conn = null;

		// STEP 2: Register JDBC driver
		Class.forName(JDBC_DRIVER);

		Scanner scanner = new Scanner(System.in);

        System.out.print("user: ");
        String username = scanner.nextLine();
        
        System.out.print("password: ");
        String password = scanner.nextLine();
        
		// STEP 3: Open a connection
		System.out.println("Check connection to database...");

       try
       {
		conn = DriverManager.getConnection(DB_URL, username, password);
		if(conn.isValid(10))
		{
			System.out.println("Successfully connected to database!");
		}
       }
       catch(Exception e)
       {
    	   
       }
			return conn;
        
	}
}