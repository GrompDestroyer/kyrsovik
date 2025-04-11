package com.example.cursachok;

import android.content.ContentValues;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.Toast;
import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import java.util.ArrayList;

public class MainActivity extends AppCompatActivity {

    private ArrayList<String> recipes = new ArrayList<>();
    private ArrayList<String> caloriesList = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_main);

        DatabaseHelper dbHelper = new DatabaseHelper(this);
        SQLiteDatabase db = dbHelper.getWritableDatabase();

        
        clearDatabase(db);


        if (isDatabaseEmpty(db)) {
            addRecipe(db, "Салат Цезарь", "Курица, Салат, Сыр, Соус", "200, 50, 100, 150");
            addRecipe(db, "Борщ", "Свекла, Картофель, Мясо, Капуста", "40, 80, 250, 30");
            addRecipe(db, "Паста Карбонара", "Спагетти, Бекон, Яйца, Сыр Пармезан", "200, 150, 70, 100");
            addRecipe(db, "Омлет с овощами", "Яйца, Помидоры, Перец, Лук", "150, 20, 15, 10");
            addRecipe(db, "Пицца Маргарита", "Тесто, Томаты, Сыр, Базилик", "300, 200, 100, 50");
            addRecipe(db, "Суп Том Ям", "Креветки, Кокосовое молоко, Лимонник, Чили", "100, 50, 200, 30");
            addRecipe(db, "Ризотто с грибами", "Рис, Грибы, Лук, Пармезан", "150, 100, 200, 50");
        }

        // Загрузка данных из базы
        Cursor cursor = db.query(DatabaseHelper.TABLE_RECIPES, null, null, null, null, null, null);
        if (cursor.moveToFirst()) {
            int nameIndex = cursor.getColumnIndex("name");
            int caloriesIndex = cursor.getColumnIndex("calories");
            do {
                recipes.add(cursor.getString(nameIndex));
                caloriesList.add(cursor.getString(caloriesIndex));
            } while (cursor.moveToNext());
        }
        cursor.close();

        ListView listView = findViewById(R.id.recipeListView);
        ArrayAdapter<String> adapter = new ArrayAdapter<>(this, android.R.layout.simple_list_item_1, recipes);
        listView.setAdapter(adapter);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                String[] calArray = caloriesList.get(position).split(", ");
                int totalCalories = 0;
                for (String cal : calArray) {
                    totalCalories += Integer.parseInt(cal);
                }
                Toast.makeText(MainActivity.this, "Всего калорий: " + totalCalories, Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void addRecipe(SQLiteDatabase db, String name, String ingredients, String calories) {
        ContentValues values = new ContentValues();
        values.put("name", name);
        values.put("ingredients", ingredients);
        values.put("calories", calories);
        db.insert(DatabaseHelper.TABLE_RECIPES, null, values);
    }

    private boolean isDatabaseEmpty(SQLiteDatabase db) {
        Cursor cursor = db.rawQuery("SELECT COUNT(*) FROM " + DatabaseHelper.TABLE_RECIPES, null);
        boolean isEmpty = true;
        if (cursor.moveToFirst()) {
            isEmpty = cursor.getInt(0) == 0;
        }
        cursor.close();
        return isEmpty;
    }

    private void clearDatabase(SQLiteDatabase db) {
        db.delete(DatabaseHelper.TABLE_RECIPES, null, null);
    }


}