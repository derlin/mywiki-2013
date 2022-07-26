#ifndef RGBLed_H
#define RGBLed_H

/**
 * The available colors
 */
enum Color_t
{
    Red,
    Green,
    Blue,
    Yellow,
    Purple,
    Cyan,
    White
};


class RGBLed
{

public:
	/**
	 * @brief Create a RGB Led driver
	 * @details  each pin will be configured as output by the module 
	 * @param r the red pin
	 * @param g the green pin
	 * @param b the blue pin
	 */
    RGBLed(int r, int g, int b);
    /**
     * @brief Turn on the led with the given color
     * @details If the led is already on, the color will just change
     * 
     * @param color The color
     */
    void on(Color_t color);
    /**
     * @brief Turn off the led
     */
    void off();

    /**
     * @brief Twinkle the light (on/off) 
     * @details The led will twinkle with the given color
     * \c times times at the given rate (in ms)
     * 
     * @param color the color
     * @param times number of times the led will flash
     * @param rate the rate
     */
    void twinkle(Color_t color, int times, int rate);

private:
    int redPin;
    int greenPin;
    int bluePin;
};

#endif