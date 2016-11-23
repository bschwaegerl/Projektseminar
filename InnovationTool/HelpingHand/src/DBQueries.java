import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Scanner;

import javax.accessibility.AccessibleHypertext;
import javax.swing.JFrame;

public class DBQueries {

	public void createDatabaseAndTables(ArrayList<String> tableNames, ArrayList<String> words, Connection conn)
			throws SQLException {

		
		String dropDatabase =  "DROP DATABASE IF EXISTS Innovation";
		String createDatabase = "CREATE DATABASE IF NOT EXISTS Innovation";
		String useDatabase = "USE Innovation";
		String dropTable = "DROP TABLE IF EXISTS `%s`";
		String createTable = "CREATE TABLE `%s` (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, word VARCHAR(255), FULLTEXT KEY word(word))";
		String insertTable = "INSERT INTO `%s` (word) VALUES ('%s')";
		String utf8Table = "ALTER TABLE `%s` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
				
		PreparedStatement pCreateDatabase = null;
		PreparedStatement pUseDatabase = null;
		PreparedStatement pDropTable = null;
		PreparedStatement pCreateTable = null;
		PreparedStatement pInsertTable = null;
		PreparedStatement pDropDatabase = null;
		PreparedStatement pUtf8Table = null;


		
        
        
		conn.setAutoCommit(false);

		pCreateDatabase = conn.prepareStatement(createDatabase);
		pDropDatabase = conn.prepareStatement(dropDatabase);
		pUseDatabase = conn.prepareStatement(useDatabase);
		//System.out.println("Dropping Database...");
		//pDropDatabase.executeUpdate();
		//System.out.println("Database dropped");
		pCreateDatabase.executeUpdate();
		pUseDatabase.executeUpdate();


		System.out.println("Created/ Use Database");

		Scanner scanner = new Scanner(System.in);

        System.out.print("Would you like to add the new words to existing tables? (y|n)");
        String answer = scanner.nextLine();
		// Tabellen erzeugen
		for (String actualTable : tableNames) {
	        
	        if(answer.equals("n") || answer.equals("y"))
	        {
	        	if(answer.equals("n"))
	        	{
	        	pDropTable = conn.prepareStatement(
						String.format(dropTable, actualTable));
				pCreateTable = conn.prepareStatement(
						String.format(createTable, actualTable));
				pUtf8Table = conn.prepareStatement(
						String.format(utf8Table, actualTable));
				

				pDropTable.executeUpdate();
				pCreateTable.executeUpdate();
				System.out.println("Created table: "+ actualTable);
	        	}
	        }
	        else
	        {
	        	while(!(answer.equals("n") || answer.equals("y")))
	        			{
	        	System.out.println("Wrong decicion! ");
	        	System.out.print("Would you like to add the new words to existing tables? (y|n)");
	            answer = scanner.nextLine();
	        			}
	        }
	        
			
		}
			// Tabelle befüllen
			for (String actualWord : words) {
				// Vergleicht die ersten drei Zeichen von Tabelle und Wort
				// bei Übereinstimmung hinzufügen/ bei keiner Übereinstimmmung
				// Abbruch
				
				String compare = (actualWord.substring(0, 2)).toLowerCase();

				
					pInsertTable = conn.prepareStatement(
							String.format(insertTable, compare, actualWord));
					
					try
					{
					pInsertTable.executeUpdate();
					} catch (Exception e )
					{
						System.out.println(actualWord +" couldnt be written in the database!");
					}
					
					System.out.println(actualWord + " added to table " + compare);

				
		}
			System.out.println("Words added to database! \n");
			System.out.println("Formatting database... \n");
			pUtf8Table.executeUpdate();
			System.out.println("Finish");
	}

}
