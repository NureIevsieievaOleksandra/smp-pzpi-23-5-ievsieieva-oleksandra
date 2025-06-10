<?php

class GroceryStore {
    private $products = [];
    private $cart = [];
    private $userName = '';
    private $userAge = 0;
    
    public function __construct() {
        $this->loadProducts();
    }
    
    private function loadProducts() {
        $jsonFile = 'products.json';
        
        if (!file_exists($jsonFile)) {
            echo "ПОМИЛКА! Файл products.json не знайдено!\n";
            exit;
        }
        
        $jsonData = file_get_contents($jsonFile);
        $this->products = json_decode($jsonData, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "ПОМИЛКА! Некоректний формат JSON файлу!\n";
            exit;
        }
    }
    
    public function run() {
        while (true) {
            $this->showMainMenu();
            $command = $this->getInput("Введіть команду: ");
            
            switch ($command) {
                case '1':
                    $this->selectProducts();
                    break;
                case '2':
                    $this->showBill();
                    break;
                case '3':
                    $this->setupProfile();
                    break;
                case '0':
                    echo "До побачення!\n";
                    exit;
                default:
                    $this->showError();
            }
        }
    }
    
    private function showMainMenu() {
        echo "\n################################\n";
        echo "# ПРОДОВОЛЬЧИЙ МАГАЗИН \"ВЕСНА\" #\n";
        echo "################################\n";
        echo "1 Вибрати товари\n";
        echo "2 Отримати підсумковий рахунок\n";
        echo "3 Налаштувати свій профіль\n";
        echo "0 Вийти з програми\n";
    }
    
    private function showError() {
        echo "ПОМИЛКА! Введіть правильну команду\n";
        echo "1 Вибрати товари\n";
        echo "2 Отримати підсумковий рахунок\n";
        echo "3 Налаштувати свій профіль\n";
        echo "0 Вийти з програми\n";
    }
    
    private function selectProducts() {
        while (true) {
            $this->showProductList();
            $productId = $this->getInput("Виберіть товар: ");
            
            if ($productId === '0') {
                break;
            }
            
            if (!isset($this->products[$productId])) {
                echo "ПОМИЛКА! ВКАЗАНО НЕПРАВИЛЬНИЙ НОМЕР ТОВАРУ\n";
                continue;
            }
            
            $product = $this->products[$productId];
            echo "Вибрано: " . $product['name'] . "\n";
            
            $quantity = $this->getInput("Введіть кількість, штук: ");
            
            if (!is_numeric($quantity) || $quantity < 0 || $quantity >= 100) {
                echo "ПОМИЛКА! Кількість повинна бути від 0 до 99\n";
                continue;
            }
            
            $quantity = intval($quantity);
            
            if ($quantity === 0) {
                if (isset($this->cart[$productId])) {
                    unset($this->cart[$productId]);
                    echo "ВИДАЛЯЮ З КОШИКА\n";
                }
                if (empty($this->cart)) {
                    echo "КОШИК ПОРОЖНІЙ\n";
                } else {
                    $this->showCart();
                }
            } else {
                $this->cart[$productId] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
                $this->showCart();
            }
        }
    }
    
    private function showProductList() {
        echo "\n№  НАЗВА                 ЦІНА\n";
        foreach ($this->products as $id => $product) {
            printf("%-2d %-20s %d\n", $id, $product['name'], $product['price']);
        }
        echo "   -----------\n";
        echo "0  ПОВЕРНУТИСЯ\n";
    }
    
    private function showCart() {
        echo "\nУ КОШИКУ:\n";
        echo "НАЗВА        КІЛЬКІСТЬ\n";
        foreach ($this->cart as $item) {
            printf("%-12s %d\n", $item['name'], $item['quantity']);
        }
    }
    
    private function showBill() {
        if (empty($this->cart)) {
            echo "\nКОШИК ПОРОЖНІЙ\n";
            return;
        }
        
        echo "\n№  НАЗВА                 ЦІНА  КІЛЬКІСТЬ  ВАРТІСТЬ\n";
        $total = 0;
        $counter = 1;
        
        foreach ($this->cart as $item) {
            $cost = $item['price'] * $item['quantity'];
            printf("%-2d %-20s %-5d %-9d %d\n", 
                $counter++, $item['name'], $item['price'], $item['quantity'], $cost);
            $total += $cost;
        }
        
        echo "РАЗОМ ДО CПЛАТИ: $total\n";
    }
    
    private function setupProfile() {
        echo "\n";
        
        while (true) {
            $name = $this->getInput("Ваше імʼя: ");
            
            if (empty(trim($name))) {
                echo "ПОМИЛКА! Імʼя не може бути порожнім\n";
                continue;
            }
            
            if (!preg_match('/[a-zA-Zа-яА-ЯіІїЇєЄ]/u', $name)) {
                echo "ПОМИЛКА! Імʼя повинно містити хоча б одну літеру\n";
                continue;
            }
            
            $this->userName = trim($name);
            break;
        }
        
        while (true) {
            $age = $this->getInput("Ваш вік: ");
            
            if (!is_numeric($age)) {
                echo "ПОМИЛКА! Вік повинен бути числом\n";
                continue;
            }
            
            $age = intval($age);
            
            if ($age < 7 || $age > 150) {
                echo "ПОМИЛКА! Вік повинен бути від 7 до 150 років\n";
                continue;
            }
            
            $this->userAge = $age;
            break;
        }
        
        echo "Профіль налаштовано: {$this->userName}, {$this->userAge} років\n";
    }
    
    private function getInput($prompt) {
        echo $prompt;
        return trim(fgets(STDIN));
    }
}

try {
    $store = new GroceryStore();
    $store->run();
} catch (Exception $e) {
    echo "ПОМИЛКА: " . $e->getMessage() . "\n";
}

?>