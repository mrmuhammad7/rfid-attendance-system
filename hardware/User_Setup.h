#define ST7789_DRIVER

#define TFT_WIDTH 240
#define TFT_HEIGHT 240

#define CGRAM_OFFSET

// ESP8266
#define TFT_MOSI D7
#define TFT_SCLK D5
#define TFT_DC D1
#define TFT_RST D0
#define TFT_CS -1

#define LOAD_GLCD
#define LOAD_FONT2
#define LOAD_FONT4
#define LOAD_FONT6
#define LOAD_FONT7
#define LOAD_FONT8

#define SPI_FREQUENCY 27000000