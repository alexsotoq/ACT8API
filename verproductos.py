from flask import Flask, jsonify
import mysql.connector
import requests

app = Flask(__name__)

DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'tienda'
}

@app.route('/')
def productos_directo():
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT * FROM productos")
        productos = cursor.fetchall()
        return jsonify({
            'fuente': 'Base de datos directa',
            'productos': productos,
            'total': len(productos)
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api')
def productos_api():
    try:
        response = requests.get('http://localhost/ACT8%20API/api.php')
        return jsonify(response.json())
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, port=5000)