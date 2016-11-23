import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.Scanner;

public class Logic {
	
	
	
		static String alphabet = "abcdefghijklmnopqrstuvwxyzßäöü";

	// Methode, um 26³ Tabellen zu erzeugen
		public static ArrayList<String> getTableNames()
		{		
			ArrayList<String> tableNames = new ArrayList<>();
			System.out.println("Generating tablenames...");

			for(int i = 0; i < alphabet.length(); i++)
			{
				for(int j = 0; j < alphabet.length(); j++)
				{
						String tableName = String.valueOf(alphabet.charAt(i)) + String.valueOf(alphabet.charAt(j));
						tableNames.add(tableName);
				}
			}
			System.out.println("Tablenames generated");
			return tableNames;
			
			
		}

		// Methode, um Wörter einzulesen
		public  static ArrayList<String> getWords() {
			BufferedReader bufferedReader;
			ArrayList<String> words = new ArrayList<>();
			System.out.println("Reading words...");

			try {

				
				bufferedReader = new BufferedReader(new FileReader(getFile()));
				
			
				String line;

				while (null != (line = bufferedReader.readLine())) {
					words.add(line);
				}

			} catch (Exception e) {
				e.printStackTrace();
			}
			System.out.println("Read words");

			return words;
		}
		public static String getFile() throws FileNotFoundException
		{
			
			Scanner scanner = new Scanner(System.in);

	        System.out.print("Type in the COMPLETE filepath (including: *.txt for example):");
	        String filePath = scanner.nextLine();
	        
	        Path p = Paths.get(filePath);
	        
	        String sFilePath =  p.getFileName().toString();
	        while (!new File(sFilePath).exists())
	        {
	        	System.out.print("Invalid path! ");
	        	System.out.print("Type in the COMPLETE filepath (including: *.txt for example):");
	 	        filePath = scanner.nextLine();
	 	        p = Paths.get(filePath);
	 	        
	 	        sFilePath =  p.getFileName().toString();
	        }
	        
			return sFilePath;
	        
		}
}
