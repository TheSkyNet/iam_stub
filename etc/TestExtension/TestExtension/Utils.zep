namespace TestExtension;

/**
 * TestExtension\Utils
 *
 * A simple utility class to demonstrate Zephir functionality
 */
class Utils
{
    /**
     * Returns a greeting message
     *
     * @param string name
     * @return string
     */
    public static function greet(string name) -> string
    {
        return "Hello, " . name . " from Zephir extension!";
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
     * Returns the current timestamp
     *
     * @return int
     */
    public static function getCurrentTime() -> int
    {
        return time();
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
}