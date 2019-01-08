<?    
    class jobsView extends view
    {
        public $model;

        function __construct(jobsModel $model)
        {
          $this->model = $model;
        }
    }
?>
