// ---------------------------------------------------------------------------

// Meranie vzdialenosti hladiny => urcenie hlbky a odoslanie dat na server

// ---------------------------------------------------------------------------

#include <NewPing.h>
#include <Wire.h>
#include <SoftwareSerial.h>

#define TRIGGER_PIN  12  // Arduino pin tied to trigger pin on the ultrasonic sensor.
#define ECHO_PIN     11  // Arduino pin tied to echo pin on the ultrasonic sensor.
#define MAX_DISTANCE 450 // Maximum distance we want to ping for (in centimeters). Maximum sensor distance is rated at 400-500cm.

//pripojenie sim900 gsm/gprs modulu, d14 a d15 (serial3)
SoftwareSerial gprsSerial(14, 15);
//pripojenie JSN-SR04T (ultrazvukovy merac)
NewPing sonar(TRIGGER_PIN, ECHO_PIN, MAX_DISTANCE); // NewPing setup of pins and maximum distance.
//MedianFilter filter(31,0);

const int POCET_MERANI_V_SERII = 21;
//urcenie intervalu merani vramci jednej serie, urci sa ako minuta / pocet merani (uvodne pred cyklom sa nepocita)
//vysledna hodnota je v milisekundach
int intervalMeraniVSerii = 60 / (POCET_MERANI_V_SERII - 1) * 1000;
int medianIndex = ((POCET_MERANI_V_SERII / 2) + 1);

//globalna premenna - pole vysledkov merani; globalna preto, aby sme ju mohli pouzivat kdekolvek
//(predovsetkym sa tym snazime vyhnut potrebe posielat pointer na pole do parametra)
unsigned int vysledokMerania[POCET_MERANI_V_SERII];

//vzdialenost meraca od hladiny
//(od nej sa odvodzuje aktualna hlbka nadrze v bode merania
unsigned int vzdialenost;

//cast "SETUP", vykonava sa len pri zavadzani noveho programu do arduina
//a to bud z IDE cez USB kabel alebo ked sa resetuje Arduino (co je v podstate
//opatovne zavedenie "noveho" programu)
void setup() {

  //monitor - vypis do serial monitoru
  Serial.begin(115200); // Open serial monitor at 115200 baud to see ping results.

  //spustenie gsm/gprs
  gprsSerial.begin(2400);
  
  // attach or detach from GPRS service 
  gprsSerial.println("AT+CGATT?");
  delay(100);
  toSerial();

  // bearer settings (sluzba = GPPRS, budeme volat internetovu url)
  gprsSerial.println("AT+SAPBR=3,1,\"CONTYPE\",\"GPRS\"");
  delay(2000);
  toSerial();

  // bearer settings (APN = access point name pre siet, v nasom pripade O2, pouziva 'o2internet')
  gprsSerial.println("AT+SAPBR=3,1,\"APN\",\"o2internet\"");
  delay(2000);
  toSerial();

  // bearer settings (povolenie/zapnutie(enable) GPRS)
  gprsSerial.println("AT+SAPBR=1,1");
  delay(2000);
  toSerial();
  
}

//cast "LOOP" - cyklicky vykonavany kod, t.j. samotny program, resp.
//funkcionalita, ktoru ma arduino a jeho komponenty vykonavat
void loop() {

  Serial.println("Meranie");
  meranie();
  Serial.println("----------------");

  Serial.println("Vypocet vzdialenosti");
  vypocetVzdialenosti();
  Serial.println("----------------");


  Serial.println("Odosielanie dat");
  odoslanieDat();
  Serial.println("----------------");

  Serial.println("Cakanie (1 minuta)");
  Serial.println("----------------");

   //cakame 1 minutu do dalsieho cyklu
   delay(60000);
}

//samostatna metoda vykonavajuca meranie
//pocas minuty urobi 21 merani, na zaciatku jedno a nasledne kazde 3 sekundy
//(z toho vychadza ta minuta -> 20 x 3s = 60s = 1 min)
void meranie() {

  //index pre for cyklus
  int i = 0;  
  
  for (i = 0; i < POCET_MERANI_V_SERII; i++) {
    vysledokMerania[i] = sonar.ping();    
    Serial.print(i+1);
    Serial.print(" => ");
    Serial.println(vysledokMerania[i]);
    delay(intervalMeraniVSerii);
  }
  
}

//bubble sort - utriedenie pola cisiel (pre 11 hodnot u nas uplne dstatocny typ triedenia)
void vypocetVzdialenosti() {
      
  int i,j, swapper;
  unsigned int median;

  //bubble sort - utriedenie pola cisiel (pre mensi pocet hodnot uplne dostatocne rychly typ triedenia)
  for(i = 0; i < POCET_MERANI_V_SERII; i++) {  // outer loop
    for(j = 0; j < (POCET_MERANI_V_SERII - i - 1); j++)  {  // inner loop
      if( vysledokMerania[j] > vysledokMerania[j+1] ) {
        // swap them:
        swapper = vysledokMerania[j];
        vysledokMerania[j] = vysledokMerania[j+1];
        vysledokMerania[j+1] = swapper;
      }
    }
  }

  Serial.print("Utriedene hodnoty - ");
  for (i = 0; i < POCET_MERANI_V_SERII; i++){
    Serial.print(vysledokMerania[i]);
    Serial.print(", ");
  }
  Serial.println();
  
  median = vysledokMerania[medianIndex]; //stredna hodnota, t.j. na presne strednom indexe (musi sa striktne pouzivat neparny pocet merani
  Serial.print("vysledny median: ");
  Serial.print( median );
  
  vzdialenost = median / US_ROUNDTRIP_CM;
  Serial.print(", vzdialenost: ");
  Serial.print( vzdialenost ); // Convert ping time to distance in cm and print result (0 = outside set distance range)
  Serial.println("cm");

}

void odoslanieDat() {
    // initialize http service
   gprsSerial.println("AT+HTTPINIT");
   delay(2000); 
   toSerial();

   // set http param value
   gprsSerial.print("AT+HTTPPARA=\"URL\",\"http://diplomovkarha.000webhostapp.com/svc/meranie.php?h=");
   gprsSerial.print(vzdialenost);
   gprsSerial.println("\"");
   delay(2000);
   toSerial();

   // set http action type 0 = GET, 1 = POST, 2 = HEAD
   gprsSerial.println("AT+HTTPACTION=0");
   delay(6000);
   toSerial();

   // read server response
   gprsSerial.println("AT+HTTPREAD"); 
   delay(1000);
   toSerial();

   gprsSerial.println("");
   gprsSerial.println("AT+HTTPTERM");
   toSerial();
   delay(300);

   gprsSerial.println(""); 
}

void toSerial()
{
  while(gprsSerial.available()!=0)
  {
    Serial.write(gprsSerial.read());
  }
}
