<?php
/**
 * survey_view.php along with index.php creates a list/view application
 * 
 * @package SM16
 * @author Caress Roche <croche02@seattlecentral.edu>
 * @version 0.1 2016/07/13
 * @link http://www.caressroche.com/
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @see infrc.php
 * @see Pager.php 
 * @todo none
 */

# '../' works for a sub-folder.  use './' for the root  
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials
 
# check variable of item passed in - if invalid data, forcibly redirect back to ice-cream-list.php page
if(isset($_GET['id']) && (int)$_GET['id'] > 0){#proper data must be on querystring
	 $myID = (int)$_GET['id']; #Convert to integer, will equate to zero if fails
}else{
	myRedirect(VIRTUAL_PATH . "surveys/index.php");
}

//sql statement to select individual item
//$sql = "select * from sm16_iceCream where IceCreamID = " . $myID;
//$sql = "select * from sm16_surveys where SurveyID = " . $myID;
//---end config area --------------------------------------------------

$foundRecord = FALSE; # Will change to true, if record found!

$mySurvey = new Survey($myID);
if($mySurvey->isValid)
{//load survey title in title tag
    $config->titleTag = $mySurvey->Title;
    
}else{//sorry no survey? put that in the title tag
    $config->titleTag = 'Sorry no such survey';
}

dumpDie($mySurvey);
 


# END CONFIG AREA ---------------------------------------------------------- 

get_header(); #defaults to theme header or header_inc.php
?>
<h3 align="center"><?=$config->titleTag?></h3>

<?php
get_footer(); #defaults to theme footer or footer_inc.php


class Survey
{
    public $Title = '';
    public $Description = '';
    public $SuveyID = 0;
    public $isValid = false;
    public $Questions = array();
    
    public function __construct($id)
    {
        //forcibly cast into an integer
        $id = (int)$id;
        $sql = "select * from sm16_surveys where SurveyID = " . $id; 
        
        $result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

        if(mysqli_num_rows($result) > 0)
        {#records exist - process
               $this->SurveyID = $id;
               $this->isValid = true;	
               while ($row = mysqli_fetch_assoc($result))
               {
                   $this->Title = dbOut($row['Title']);
                   $this->Description = dbOut($row['Description']);
            
               }
        }

        @mysqli_free_result($result); # We're done with the data!
        
        
        //Start of questions work
        
        /*
        select q.QuestionID, q.Question from sm16_questions q inner join sm16_surveys s on s.SurveyID = q.SurveyID where s.SurveyID = 1
        */
        
        
        
        
        $sql = "select q.QuestionID, q.Question, q.Description from sm16_questions q inner join sm16_surveys s on s.SurveyID = q.SurveyID where s.SurveyID = " . $id; 
        //$sql = "select * from sm16_surveys where SurveyID = " . $id; 
        
        $result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

        if(mysqli_num_rows($result) > 0)
        {#records exist - process
               //$this->SurveyID = $id;
               //$this->isValid = true;	
               while ($row = mysqli_fetch_assoc($result))
               {
                   //$this->Title = dbOut($row['Title']);
                   //$this->Description = dbOut($row['Description']);
                    $this->Questions[] = new Question($row['QuestionID'], dbOut($row['Question']), dbOut($row['Description']));
               }
        }

        @mysqli_free_result($result); # We're done with the data!

        
        
        //End of quesitons work

    } #end survey constructor
    
    
} #end Survey class

class Question 
{
    public $QuestionID = 0;
    public $Text = '';
    public $Description = '';
    
    public function __construct($QuestionID, $Text, $Description)
    {
        $this->QuestionID = $QuestionID;
        $this->Text = $Text;
        $this->Description = $Description;
    
    }//End question constructor
    
}//End questions class