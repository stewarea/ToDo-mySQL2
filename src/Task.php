<?php
class Task

{

    private $description;
    private $id;
    private $completed;
    private $due_date;

    function __construct($description, $id = null, $completed = 0, $due_date)
    {
        $this->description = $description;
        $this->id = $id;
        $this->completed = $completed;
        $this->due_date = date_create($due_date);

    }

    function setDescription($new_description)
    {
        $this->description = (string) $new_description;
    }

    function getDescription()
    {
        return $this->description;
    }

    function getId()
    {
        return $this->id;
    }

    function getCompleted()
    {
        return $this->completed;
    }

    function setDueDate($new_due_date)
    {
        $this->due_date = date_create($new_due_date);
    }

    function getDueDate()
    {
        return date_format($this->due_date,'Y-m-d');
    }

    function save()
    {
        $GLOBALS['DB']->exec("INSERT INTO tasks (description, completed, due_date) VALUES ('{$this->getDescription()}', {$this->getCompleted()}, '{$this->getDueDate()}');");
        $this->id = $GLOBALS['DB']->lastInsertId();
    }

    static function getAll()
    {
        $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks;");
        $tasks = array();
        foreach($returned_tasks as $task) {
            $description = $task['description'];
            $id = $task['id'];
            $due_date = $task['due_date'];
            $completed = $task['completed'];
            $new_task = new Task($description, $id, $completed, $due_date);
            array_push($tasks, $new_task);
        }
            return $tasks;
    }

    static function getIncomplete()
    {
        $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks WHERE completed = 0 ORDER BY due_date;");
        $tasks = array();
        foreach($returned_tasks as $task) {
            $description = $task['description'];
            $id = $task['id'];
            $completed = 0;
            $due_date = $task['due_date'];
            $new_task = new Task($description, $id, $completed, $due_date);
            array_push($tasks, $new_task);
        }
            return $tasks;
    }

    static function getComplete()
    {
        $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks WHERE completed = 1;");
        $tasks = array();
        foreach($returned_tasks as $task) {
            $description = $task['description'];
            $id = $task['id'];
            $completed = 1;
            $due_date = $task['due_date'];
            $new_task = new Task($description, $id, $completed, $due_date);
            array_push($tasks, $new_task);
        }
            return $tasks;
    }


    static function deleteAll()
    {
        $GLOBALS['DB']->exec("DELETE FROM tasks;");
    }

    function update($new_description)
    {
        $GLOBALS['DB']->exec("UPDATE tasks SET description = '{$new_description}' WHERE id = {$this->getId()};");
        $this->setDescription($new_description);
    }

    function updateCompleted()
    {
        $GLOBALS['DB']->exec("UPDATE tasks SET completed = 1 WHERE id = {$this->getId()};");
    }

    function delete()
    {
        $GLOBALS['DB']->exec("DELETE FROM tasks WHERE id = {$this->getId()};");
    }

    static function find($search_id)
    {
        $found_task = null;
        $tasks = Task::getAll();
        foreach($tasks as $task) {
            $task_id = $task->getId();
            if ($task_id == $search_id) {
                $found_task = $task;
            }
        }
        return $found_task;
    }
    function getCategories()
    {
        $query = $GLOBALS['DB']->query("SELECT category_id FROM categories_tasks WHERE task_id = {$this->getId()};");
        $category_ids = $query->fetchAll(PDO::FETCH_ASSOC);

        $categories = array();
        foreach($category_ids as $id) {
            $category_id = $id['category_id'];
            $result = $GLOBALS['DB']->query("SELECT * FROM categories WHERE id = {$category_id};");
            $returned_category = $result->fetchAll(PDO::FETCH_ASSOC);

            $name = $returned_category[0]['name'];
            $id = $returned_category[0]['id'];
            $new_category = new Category($name, $id);
            array_push($categories, $new_category);
        }
        return $categories;
    }
    function addCategory($category)
    {
        $GLOBALS['DB']->exec("INSERT INTO categories_tasks (category_id, task_id) VALUES ({$category->getId()}, {$this->getId()});");
    }


}
?>
