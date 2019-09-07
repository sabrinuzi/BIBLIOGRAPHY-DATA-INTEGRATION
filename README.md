# BIBLIOGRAPHY-DATA-INTEGRATION
Nowadays there is a lot of information in the Web. For this reason, getting to the right information is becoming a challenge.
This work presents the opportunity of using API of several websites which are the main source of different academic publications as a tool to achieve the wanted information.
This method serves also as a filter to eliminate the useless information and is focused only on the academic materials. 
During this work several sources that serve the data through API have been included.  
This system integrates several online science related sources like Springer, Elsevier CiNii and DBLP in a single system. 


![Home page](screnshot/Screenshot_27.png?raw=true "Main page")
![Reuslt page](screnshot/results.png?raw=true "Result page")

### Run docker
Build docker image:
`docker build -t snuzi-bdi .`


Run docker image:
`docker run -p 8890:80 snuzi-bdi`


Access the site:
`localhost:8890`