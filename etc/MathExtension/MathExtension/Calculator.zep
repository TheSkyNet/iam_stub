namespace MathExtension;

/**
 * MathExtension\Calculator
 *
 * A utility class for MathExtension extension
 */
class Calculator
{
    /**
     * Returns a greeting message
     *
     * @param string name
     * @return string
     */
    public static function greet(string name) -> string
    {
        return "Hello, " . name . " from MathExtension extension!";
    }

    /**
     * Adds two numbers
     *
     * @param int a
     * @param int b
     * @return int
     */
    public static function add(int a, int b) -> int
    {
        return a + b;
    }

    /**
     * Multiplies two numbers
     *
     * @param int a
     * @param int b
     * @return int
     */
    public static function multiply(int a, int b) -> int
    {
        return a * b;
    }

    /**
     * Checks if a string is empty
     *
     * @param string str
     * @return bool
     */
    public static function isEmpty(string str) -> bool
    {
        return strlen(str) == 0;
    }

    /**
     * Returns the current timestamp
     *
     * @return int
     */
    public static function getCurrentTime() -> int
    {
        return time();
    }

    /**
     * Converts string to uppercase
     *
     * @param string str
     * @return string
     */
    public static function toUpper(string str) -> string
    {
        return strtoupper(str);
    }

    /**
     * Converts string to lowercase
     *
     * @param string str
     * @return string
     */
    public static function toLower(string str) -> string
    {
        return strtolower(str);
    }
}