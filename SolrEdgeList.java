import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Set;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

public class SolrEdgeList {

	public static void main(String[] args) throws IOException {
		// TODO Auto-generated method stub
		File directory=new File("/Users/saigeetha/Desktop/hw3/crawl/");
		HashMap<String,String> map1=new HashMap<String,String>();
		HashMap<String,String> map2=new HashMap<String,String>();
		
		Set<String> edges=new HashSet<String>();
		File merge=new File("/Users/saigeetha/Desktop/hw3/mergeDataFile.csv");
		BufferedReader reader=new BufferedReader(new FileReader(merge));
		BufferedWriter writer=new BufferedWriter(new FileWriter("/Users/saigeetha/Desktop/edgelist.txt"));
		String f=null;
		
		while((f=reader.readLine())!=null) {
			String fileContent[]=f.split(",");
			map1.put(fileContent[0], fileContent[1]);
			map2.put(fileContent[1], fileContent[0]);
		}
		
		for(File file:directory.listFiles()) {
			if(file.getName().contains(".DS")) {
				continue;
			} else {
				Document d=Jsoup.parse(file,"UTF-8",map1.get(file.getName()));
				Elements links=d.select("a[href]");
				for(Element link:links) {
					String url=link.attr("abs:href").trim();
					if(map2.containsKey(url)) {
						edges.add(file.getName()+" "+map2.get(url));
					}
				}
			}
		}
		
		
		for(String str:edges) {
			writer.write(str);
			writer.newLine();
		}
		writer.flush();
		writer.close();
		reader.close();
		System.out.println("----end----");
	}

}
