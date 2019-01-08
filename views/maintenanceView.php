<?    
    class maintenanceView extends view
    {
        public $model;

        function __construct(maintenanceModel $model)
        {
          $this->model = $model;
        }
    }
?>
