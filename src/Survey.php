<?php
/**
 * Survey management class
 */

namespace CallSurvey;

class Survey {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    /**
     * Create a new survey
     */
    public function create($title, $description = '') {
        $sql = "INSERT INTO surveys (title, description) VALUES (?, ?)";
        $this->db->query($sql, [$title, $description]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Get survey by ID
     */
    public function get($id) {
        $sql = "SELECT * FROM surveys WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Get all active surveys
     */
    public function getActive() {
        $sql = "SELECT * FROM surveys WHERE active = 1 ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Add question to survey
     */
    public function addQuestion($surveyId, $questionText, $questionType = 'rating', $order = 0) {
        $sql = "INSERT INTO questions (survey_id, question_text, question_type, question_order) 
                VALUES (?, ?, ?, ?)";
        $this->db->query($sql, [$surveyId, $questionText, $questionType, $order]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Get all questions for a survey
     */
    public function getQuestions($surveyId) {
        $sql = "SELECT * FROM questions WHERE survey_id = ? ORDER BY question_order";
        return $this->db->fetchAll($sql, [$surveyId]);
    }
    
    /**
     * Start a new response
     */
    public function startResponse($surveyId, $phoneNumber, $channel) {
        $sql = "INSERT INTO responses (survey_id, phone_number, channel) VALUES (?, ?, ?)";
        $this->db->query($sql, [$surveyId, $phoneNumber, $channel]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Save answer to question
     */
    public function saveAnswer($responseId, $questionId, $answerValue) {
        $sql = "INSERT INTO answers (response_id, question_id, answer_value) VALUES (?, ?, ?)";
        $this->db->query($sql, [$responseId, $questionId, $answerValue]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Complete a response
     */
    public function completeResponse($responseId) {
        $sql = "UPDATE responses SET status = 'completed', completed_at = NOW() WHERE id = ?";
        $this->db->query($sql, [$responseId]);
    }
    
    /**
     * Get survey results
     */
    public function getResults($surveyId) {
        $sql = "SELECT r.*, 
                (SELECT COUNT(*) FROM answers a WHERE a.response_id = r.id) as answer_count
                FROM responses r 
                WHERE r.survey_id = ? 
                ORDER BY r.started_at DESC";
        return $this->db->fetchAll($sql, [$surveyId]);
    }
}
