from PIL import Image
import os

def resize_image(image_path, aspect_ratio):
    img = Image.open(image_path)
    
    # Convert image to RGB mode if it's RGBA
    if img.mode == 'RGBA':
        img = img.convert('RGB')

    width, height = img.size

    if aspect_ratio == "4:3":
        new_width = int((4 / 3) * height)
        new_height = height
    elif aspect_ratio == "16:9":
        new_width = int((16 / 9) * height)
        new_height = height
    else:
        print("Invalid aspect ratio selection.")
        return

    resized_img = img.resize((new_width, new_height))
    
    # Get the directory of the original image
    directory = os.path.dirname(image_path)
    
    # Generate a random filename for the resized image
    import uuid
    random_filename = str(uuid.uuid4()) + ".jpg"
    new_filepath = os.path.join(directory, random_filename)
    
    # Save the resized image with a new filename
    target_size = 3 * 1024 * 1024  # 3MB in bytes
    quality = 95  # Initial quality
    while True:
        resized_img.save(new_filepath, quality=quality)
        if os.path.getsize(new_filepath) <= target_size:
            break
        # Reduce quality until target size is reached
        quality -= 5
    
    print("Image resized and saved as:", new_filepath)

def main():
    image_path = input("Enter the path to the image: ")
    aspect_ratio = input("Choose aspect ratio (4:3 or 16:9): ")

    resize_image(image_path, aspect_ratio)

if __name__ == "__main__":
    main()
