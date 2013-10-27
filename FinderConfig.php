 <?php
/**
 * Description of FinderConfig
 *
 * Parses ini files to a FinderConfig Object
 *
 * The main instance of FinderConfig is a Singleton (and could only exist once)
 * The instance could have multiple instances of FinderConfig within it self, so
 * that multi dimentional array's can be parsed as a multy layered object
 *
 * @author niele
 *
 * @todo extract the path to the ini file and make it variable
 *
 */
class FinderConfig
{
    /**
     * Path to default config file
     */
    const DEFAULT_CONFIG_FILE = 'config.ini';



    /**
     * PlaceHolder for self (Singleton)
     *
     * @var FinderConfig
     */
    public static $instance;



    /**
     * Get the Instance of self
     *
     * @return FinderConfig
     */
    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }



    /**
     * Constructor Class
     *
     * @param array $config
     * @access private
     */
    private function __construct(array $options=array())
    {
        if (empty($options)) {
            $options = parse_ini_file(
                APPLICATION_PATH . self::DEFAULT_CONFIG_FILE,
                true
            );
        }

        $this->setConfig($options);
    }



    /**
     * Set config options, sets recurivly if it's a multydimentional array
     *
     * @param array $options
     * @access private
     * @return FinderConfig
     */
    private function setConfig(array $options=array())
    {
        foreach ($options as $key => $value) {

            if (is_array($value) && !empty($value)) {
                $value = new self($value);
            }

            $this->$key = $value;
        }

        return $this;
    }



    /**
     * Magic method get
     *
     * This gets triggered if there is a call made to an undefined property in
     * the FinderConfig instance or subInstance, so we throw an Exception
     *
     * @param string $name
     * @throws Exception
     */
    public function __get($name)
    {
        throw new Exception('call to undefined property: ' . $name);
    }
}
