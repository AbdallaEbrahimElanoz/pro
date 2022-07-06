<?php
class Database{
    
    public function dbConnection(){
        
        try{
           $conn = new PDO( "sqlsrv:Server=DESKTOP-TALQPGS;Database=php_auth_api;TrustServerCertificate=true", NULL, NULL);   
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
            return $conn;
        }
        catch(PDOException $e){
            echo "Connection error ".$e->getMessage(); 
            exit;
        }
          
    }
}