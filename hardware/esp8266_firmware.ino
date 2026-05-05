#include <SPI.h>
#include <MFRC522.h>
#include <TFT_eSPI.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

// ── Pins ─────────────────────────────────────────────────────
#define SS_PIN D8
#define RST_PIN D3
#define BUZZER D2
#define RGB_RED D4
#define RGB_GREEN 3

// ── WiFi & Server ─────────────────────────────────────────────
const char *WIFI_SSID = "YOUR_WIFI";
const char *WIFI_PASSWORD = "YOUR_PASSWORD";
const char *SERVER_URL = "http://your-server/api/scan.php";

// ── Hardware ─────────────────────────────────────────────────
MFRC522 rfid(SS_PIN, RST_PIN);
TFT_eSPI tft = TFT_eSPI();

// ── RGB helpers ───────────────────────────────────────────────
void setRGB(bool r, bool g)
{
  analogWrite(RGB_RED, r ? 0 : 255); // common anode: LOW = ON
  analogWrite(RGB_GREEN, g ? 0 : 255);
}
void clearRGB()
{
  analogWrite(RGB_RED, 255);
  analogWrite(RGB_GREEN, 255);
}

// ── Buzzer ────────────────────────────────────────────────────
void beepSuccess()
{
  digitalWrite(BUZZER, HIGH);
  delay(200);
  digitalWrite(BUZZER, LOW);
}

void beepError()
{
  for (int i = 0; i < 2; i++)
  {
    digitalWrite(BUZZER, HIGH);
    delay(100);
    digitalWrite(BUZZER, LOW);
    delay(100);
  }
}

// ── TFT helpers ───────────────────────────────────────────────
void tftClear() { tft.fillScreen(TFT_BLACK); }

void showWaiting()
{
  tftClear();
  tft.setTextColor(TFT_DARKGREY);
  tft.setTextDatum(MC_DATUM);
  tft.drawString("Scan Card", tft.width() / 2, tft.height() / 2 - 16, 4);
  tft.setTextColor(TFT_BLUE);
  tft.drawString("Ready", tft.width() / 2, tft.height() / 2 + 20, 2);
  clearRGB();
}

void showConnecting()
{
  tftClear();
  tft.setTextDatum(MC_DATUM);
  tft.setTextColor(TFT_YELLOW);
  tft.drawString("Connecting", tft.width() / 2, tft.height() / 2 - 15, 4);
  tft.setTextColor(TFT_DARKGREY);
  tft.drawString("WiFi...", tft.width() / 2, tft.height() / 2 + 20, 2);
}

void showSuccess(String name, String sid)
{
  tftClear();
  tft.setTextDatum(MC_DATUM);
  tft.setTextColor(TFT_GREEN);
  tft.drawString("Welcome!", tft.width() / 2, 50, 4);
  tft.setTextColor(TFT_WHITE);
  tft.drawString(name, tft.width() / 2, 110, 4);
  tft.setTextColor(TFT_DARKGREY);
  tft.drawString(sid, tft.width() / 2, 155, 2);
  tft.setTextColor(TFT_GREEN);
  tft.drawString("Attendance Marked", tft.width() / 2, 195, 2);
}

void showAlready(String name)
{
  tftClear();
  tft.setTextDatum(MC_DATUM);
  tft.setTextColor(TFT_YELLOW);
  tft.drawString("Already", tft.width() / 2, 80, 4);
  tft.drawString("Marked", tft.width() / 2, 125, 4);
  tft.setTextColor(TFT_DARKGREY);
  tft.drawString(name, tft.width() / 2, 175, 2);
}

void showUnknown(String uid)
{
  tftClear();
  tft.setTextDatum(MC_DATUM);
  tft.setTextColor(TFT_RED);
  tft.drawString("Unknown", tft.width() / 2, 80, 4);
  tft.drawString("Card", tft.width() / 2, 125, 4);
  tft.setTextColor(TFT_DARKGREY);
  tft.drawString(uid, tft.width() / 2, 185, 2);
}

// ── Parse simple JSON value ───────────────────────────────────
String parseJSON(String json, String key)
{
  String search = "\"" + key + "\":\"";
  int idx = json.indexOf(search);
  if (idx == -1)
    return "";
  idx += search.length();
  int end = json.indexOf("\"", idx);
  return json.substring(idx, end);
}

// ── Send UID to server ────────────────────────────────────────
void sendUID(String uid)
{
  if (WiFi.status() != WL_CONNECTED)
  {
    Serial.println("[WiFi] Not connected, skipping");
    return;
  }

  WiFiClient client;
  HTTPClient http;
  http.begin(client, SERVER_URL);
  http.addHeader("Content-Type", "application/json");
  http.setTimeout(5000);

  String payload = "{\"uid\":\"" + uid + "\"}";
  int code = http.POST(payload);

  Serial.print("[HTTP] Code: ");
  Serial.println(code);

  if (code == 200)
  {
    String body = http.getString();
    Serial.println("[HTTP] Response: " + body);

    String status = parseJSON(body, "status");
    String name = parseJSON(body, "name");
    String sid = parseJSON(body, "student_id");

    if (status == "success")
    {
      beepSuccess();
      setRGB(false, true); // Green
      // Check if already marked
      if (body.indexOf("\"already\":true") >= 0)
      {
        showAlready(name);
      }
      else
      {
        showSuccess(name, sid);
      }
    }
    else
    {
      // unknown card
      beepError();
      setRGB(true, false); // Red
      showUnknown(uid);
    }
  }
  else
  {
    Serial.println("[HTTP] Error: " + http.errorToString(code));
    beepError();
    tftClear();
    tft.setTextDatum(MC_DATUM);
    tft.setTextColor(TFT_RED);
    tft.drawString("Server Error", tft.width() / 2, tft.height() / 2, 4);
  }

  http.end();
}

// ── Setup ─────────────────────────────────────────────────────
void setup()
{
  Serial.begin(115200);
  delay(100);

  // TFT
  tft.init();
  tft.setRotation(0);
  tftClear();

  // Pins
  pinMode(BUZZER, OUTPUT);
  pinMode(RGB_RED, OUTPUT);
  pinMode(RGB_GREEN, OUTPUT);
  clearRGB();

  // WiFi
  showConnecting();
  Serial.print("[WiFi] Connecting");
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  int tries = 0;
  while (WiFi.status() != WL_CONNECTED && tries < 30)
  {
    delay(500);
    Serial.print(".");
    tries++;
  }

  if (WiFi.status() == WL_CONNECTED)
  {
    Serial.println("\n[WiFi] Connected: " + WiFi.SSID());

    tftClear();
    tft.setTextDatum(MC_DATUM);
    tft.setTextColor(TFT_GREEN);
    tft.drawString("Connected!", tft.width() / 2, tft.height() / 2 - 15, 4);
    delay(1500);
  }
  else
  {
    Serial.println("\n[WiFi] FAILED - running offline");
    tftClear();
    tft.setTextDatum(MC_DATUM);
    tft.setTextColor(TFT_RED);
    tft.drawString("WiFi Failed", tft.width() / 2, tft.height() / 2, 4);
    delay(2000);
  }

  // RFID
  SPI.begin();
  rfid.PCD_Init();
  Serial.println("[RFID] Reader ready");

  showWaiting();
}

// ── Loop ──────────────────────────────────────────────────────
void loop()
{
  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial())
    return;

  // Build UID string
  String uid = "";
  for (byte i = 0; i < rfid.uid.size; i++)
  {
    if (rfid.uid.uidByte[i] < 0x10)
      uid += "0";
    uid += String(rfid.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  Serial.println("UID: " + uid);

  sendUID(uid);

  delay(2500);
  showWaiting();

  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
}
